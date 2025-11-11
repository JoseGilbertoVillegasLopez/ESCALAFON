<?php

namespace App\Service;

use App\Repository\InformacionPersonalRepository;
use App\Repository\VacantesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InformacionPersonal;


/**
 * Servicio principal del módulo Escalafón.
 *
 * Calcula y organiza toda la información necesaria para mostrar
 * a los trabajadores ordenados por mérito dentro de su categoría.
 */
class EscalafonService
{
    private InformacionPersonalRepository $personalRepo;
    private VacantesRepository $vacanteRepo;
    private EntityManagerInterface $em;

    public function __construct(
        InformacionPersonalRepository $personalRepo,
        VacantesRepository $vacanteRepo,
        EntityManagerInterface $em
    ) {
        $this->personalRepo = $personalRepo;
        $this->vacanteRepo = $vacanteRepo;
        $this->em = $em;
    }

    /**
     * Devuelve los datos del escalafón.
     *
     * @param int|null $categoriaId Filtrar por categoría (opcional)
     * @param int $page Página actual
     * @param int $perPage Registros por página
     * @return array Estructura con trabajadores ordenados por puntaje
     */
    public function getEscalafonData(?int $categoriaId, int $page = 1, int $perPage = 20, ?string $nombre = null): array
{
    $qb = $this->em->createQueryBuilder()
        ->select('p', 'il', 'pu', 'c')
        ->from('App\Entity\InformacionPersonal', 'p')
        ->leftJoin('p.informacionLaboral', 'il')
        ->leftJoin('il.puesto', 'pu')
        ->leftJoin('il.categoria', 'c');

    // 🔍 Filtro por nombre
    if (!empty($nombre)) {
        $qb->andWhere('
            p.nombre LIKE :q
            OR p.apellidoPaterno LIKE :q
            OR p.apellidoMaterno LIKE :q
        ')
        ->setParameter('q', '%' . $nombre . '%');
    }

    // 🧱 Filtro por categoría
    if (!empty($categoriaId)) {
        $qb->andWhere('c.id = :categoria')
           ->setParameter('categoria', $categoriaId);
    }

    // Orden base
    $qb->addOrderBy('p.apellidoPaterno', 'ASC')
       ->addOrderBy('p.apellidoMaterno', 'ASC')
       ->addOrderBy('p.nombre', 'ASC');

    // Total
    $countQb = clone $qb;
    $total = (int) $countQb->resetDQLPart('select')->select('COUNT(p.id)')
        ->getQuery()->getSingleScalarResult();

    // Paginación
    $qb->setFirstResult(($page - 1) * $perPage)
       ->setMaxResults($perPage);

    $trabajadores = $qb->getQuery()->getResult();

    // 🔹 Procesar cada trabajador
    $items = [];
    foreach ($trabajadores as $t) {
        $laboral = $t->getInformacionLaboral();
        $puesto  = $laboral?->getPuesto();
        $categoria = $laboral?->getCategoria();
        $fechaIngreso = $laboral?->getFechaIncorporacion();

        // Antigüedad
        $antiguedadTexto = 'Sin registro';
        $puntosAntiguedad = 0;
        if ($fechaIngreso instanceof \DateTimeInterface) {
            $hoy = new \DateTime();
            $diff = $hoy->diff($fechaIngreso);
            $puntosAntiguedad = $diff->y;

            $partes = [];
            if ($diff->y > 0) $partes[] = $diff->y.' año'.($diff->y>1?'s':'');
            if ($diff->m > 0) $partes[] = $diff->m.' mes'.($diff->m>1?'es':'');
            if ($diff->d > 0) $partes[] = $diff->d.' día'.($diff->d>1?'s':'');
            $antiguedadTexto = count($partes) ? implode(', ', $partes) : 'Menos de un día';
        }

        // 🎓 Capacitaciones
        $cursosHechosIds = [];
        $puntosCapacitacion = 0;
        foreach ($t->getCapacitacion() as $cap) {
            $curso = $cap->getCurso();
            if ($curso) {
                $cursosHechosIds[] = $curso->getId();
                $puntosCapacitacion += $curso->getValor() ?? 0;
            }
        }

        // ⚠️ Sanciones
        $puntosSancion = 0;
        foreach ($t->getHistorialSanciones() as $sancion) {
            $puntosSancion += $sancion->getPuntosSancion() ?? 0;
        }

        // 🧮 Puntaje total
        $puntajeTotal = $puntosAntiguedad + $puntosCapacitacion - $puntosSancion;

        // ✅ Vacantes donde cumple TODOS los requisitos
        $vacantesCumple = [];
        if ($categoria) {
            $vacantes = $this->vacanteRepo->findBy(['categoria' => $categoria, 'activo' => true]);

            foreach ($vacantes as $v) {
                $cumpleTodo = true;
                foreach ($v->getRequisitos() as $req) {
                    $curso = $req->getCurso();
                    if (!$curso || !in_array($curso->getId(), $cursosHechosIds, true)) {
                        $cumpleTodo = false;
                        break;
                    }
                }

                if ($cumpleTodo && $v->getVacantesLibres() > 0) {
                    $vacantesCumple[] = $v->getNombre();
                }
            }
        }

        // 🧩 Resultado final por trabajador
        $items[] = [
            'id' => $t->getId(),
            'nombre' => (string) $t,
            'puesto' => $puesto?->getNombre() ?? 'Sin puesto',
            'categoria' => $categoria?->getNombre() ?? 'Sin categoría',
            'fecha_ingreso' => $fechaIngreso?->format('d/m/Y'),
            'antiguedad' => $antiguedadTexto,
            'puntos_capacitacion' => $puntosCapacitacion,
            'puntos_sancion' => $puntosSancion,
            'puntaje_total' => $puntajeTotal,
            'vacantes' => $vacantesCumple, // 👈 solo las que cumple todos los cursos
        ];
    }

    return [
        'items' => $items,
        'total' => $total,
        'total_pages' => (int) ceil($total / $perPage),
    ];
}





    /**
     * Determina qué vacantes son elegibles para un trabajador.
     * Solo se consideran vacantes activas dentro de la misma categoría.
     */
    private function getVacantesElegibles($categoria, $trabajador): array
    {
        if (!$categoria) {
            return [];
        }

        $vacantes = $this->vacanteRepo->findBy([
            'categoria' => $categoria,
            'activo'    => true,
        ]);

        $vacantesElegibles = [];

        foreach ($vacantes as $vacante) {
            // Validar requisitos (cursos requeridos)
            $cumple = true;
            foreach ($vacante->getRequisitos() as $req) {
                $cursoReq = $req->getCurso();
                $tiene = false;

                // Buscar si el trabajador tiene ese curso
                foreach ($trabajador->getCapacitacion() as $cap) {
                    if ($cap->getCurso() && $cap->getCurso()->getId() === $cursoReq->getId()) {
                        $tiene = true;
                        break;
                    }
                }

                if (!$tiene) {
                    $cumple = false;
                    break;
                }
            }

            if ($cumple) {
                $vacantesElegibles[] = $vacante->getNombre();
            }
        }

        return $vacantesElegibles;
    }

 public function getDetalleEscalafon(InformacionPersonal $t): array
    {
        $laboral      = $t->getInformacionLaboral();
        $puesto       = $laboral?->getPuesto();
        $categoria    = $laboral?->getCategoria();
        $fechaIngreso = $laboral?->getFechaIncorporacion();

        // 🧮 Antigüedad (años/meses)
        $antiguedad = 'Sin registro';
        if ($fechaIngreso instanceof \DateTimeInterface) {
            $diff       = (new \DateTime())->diff($fechaIngreso);
            $antiguedad = sprintf('%d años, %d meses', $diff->y, $diff->m);
        }

        // 🎓 Capacitaciones completadas por el trabajador (ids de curso)
        $cursosHechosIds = [];
        $capacitaciones  = [];
        foreach ($t->getCapacitacion() as $cap) {
            $curso = $cap->getCurso();
            if ($curso) {
                $cursosHechosIds[] = $curso->getId();
                $capacitaciones[]   = [
                    'nombre' => $curso->getNombre(),
                    'valor'  => $curso->getValor(), // por si quieres mostrarlo
                ];
            }
        }

        // ⚠️ Sanciones
        $sanciones = [];
        foreach ($t->getHistorialSanciones() as $s) {
            $sanciones[] = [
                'fecha'  => $s->getFecha()?->format('d/m/Y'),
                'motivo' => $s->getMotivo(),
                'puntos' => $s->getPuntosSancion(),
            ];
        }

        // ⬆️ Historial de ascensos
        $ascensos = [];
        foreach ($t->getHistorialAscensos() as $a) {
            $ascensos[] = [
                'fecha'           => $a->getFecha()?->format('d/m/Y'),
                'puesto_anterior' => $a->getPuestoAnterior(),
                'puesto_ascenso'  => $a->getPuestoAscenso(),
            ];
        }

        // ✅ Vacantes según requisitos (cursos)
        $vacantesDisponibles  = []; // cumple todos los requisitos
        $vacantesNoElegibles  = []; // le faltan cursos
        $vacantes             = $categoria ? $this->vacanteRepo->findBy(['categoria' => $categoria]) : [];

        foreach ($vacantes as $v) {
            if (!$v->isActivo()) {
                continue;
            }

            // Requisitos de la vacante (asumo $v->getRequisitos() y cada requisito tiene ->getCurso())
            $detalleReq = [];
            $faltantes  = [];
            $cumpleTodo = true;

            foreach ($v->getRequisitos() as $req) {
                $curso = $req->getCurso();
                if (!$curso) {
                    // por si hubiera requisitos mal cargados
                    $detalleReq[] = ['nombre' => '(requisito sin curso)', 'tiene' => false];
                    $cumpleTodo   = false;
                    continue;
                }

                $tiene = in_array($curso->getId(), $cursosHechosIds, true);
                $detalleReq[] = ['nombre' => $curso->getNombre(), 'tiene' => $tiene];

                if (!$tiene) {
                    $faltantes[] = $curso->getNombre();
                    $cumpleTodo  = false;
                }
            }

            $vacInfoBase = [
                'id'        => $v->getId(),
                'nombre'    => $v->getNombre(),
                'requisitos'=> $detalleReq,
            ];

            if ($cumpleTodo) {
                $vacantesDisponibles[] = $vacInfoBase + [
                    'faltantes'          => [],
                    'es_top_antiguedad'  => $this->esTopAntiguedadEnCategoria($categoria?->getId(), $fechaIngreso),
                ];
            } else {
                $vacantesNoElegibles[] = $vacInfoBase + [
                    'faltantes' => $faltantes,
                ];
            }
        }

        // Orden: primero las disponibles, luego las no elegibles (por nombre)
        usort($vacantesDisponibles, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
        usort($vacantesNoElegibles, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));

        return [
            // cabecera
            'trabajador'    => (string) $t,
            'trabajador_id'     => $t->getId(),
            'puesto'        => $puesto?->getNombre() ?? 'Sin puesto',
            'categoria'     => $categoria?->getNombre() ?? 'Sin categoría',
            'antiguedad'    => $antiguedad,

            // listas
            'capacitaciones'       => $capacitaciones,
            'sanciones'            => $sanciones,
            'ascensos'             => $ascensos,
            'vacantes_disponibles' => $vacantesDisponibles,
            'vacantes_no_elegibles'=> $vacantesNoElegibles,
        ];
    }

    /**
     * Verdadero si el trabajador tiene la fecha de incorporación más antigua
     * dentro de la misma categoría.
     */
    private function esTopAntiguedadEnCategoria(?int $categoriaId, ?\DateTimeInterface $miFecha): bool
{
    if (!$categoriaId || !$miFecha) {
        return false;
    }

    // Cuenta cuántos tienen incorporación ANTES que la suya (más antiguos)
    $qb = $this->em->createQueryBuilder();
    $qb->select('COUNT(il2.id)')
        ->from('App\Entity\InformacionLaboral', 'il2')
        ->where('il2.categoria = :cat')
        ->andWhere('il2.fechaIncorporacion < :mi')
        ->setParameter('cat', $categoriaId)
        ->setParameter('mi', $miFecha);

    $anteriores = (int) $qb->getQuery()->getSingleScalarResult();

    return $anteriores === 0; // Nadie más antiguo → es top
}



}

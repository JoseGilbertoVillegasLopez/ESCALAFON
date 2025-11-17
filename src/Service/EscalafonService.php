<?php

namespace App\Service;

use App\Repository\InformacionPersonalRepository;
use App\Repository\VacantesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InformacionPersonal;

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
        $this->vacanteRepo  = $vacanteRepo;
        $this->em           = $em;
    }

    /**
     * Devuelve ranking del escalafón con orden correcto por:
     *  1) Puntaje total DESC
     *  2) Antigüedad DESC
     *  3) Nombre ASC
     */
    public function getEscalafonData(
        ?int $categoriaId,
        int $page = 1,
        int $perPage = 20,
        ?string $nombre = null
    ): array
    {
        // 1️⃣ Obtener base de datos filtrada
        $qb = $this->em->createQueryBuilder()
            ->select('p', 'il', 'pu', 'c')
            ->from('App\Entity\InformacionPersonal', 'p')
            ->leftJoin('p.informacionLaboral', 'il')
            ->leftJoin('il.puesto', 'pu')
            ->leftJoin('il.categoria', 'c');

        if (!empty($nombre)) {
            $qb->andWhere("
                p.nombre LIKE :q OR 
                p.apellidoPaterno LIKE :q OR
                p.apellidoMaterno LIKE :q
            ")->setParameter('q', '%' . $nombre . '%');
        }

        if (!empty($categoriaId)) {
            $qb->andWhere('c.id = :cat')
               ->setParameter('cat', $categoriaId);
        }

        $trabajadores = $qb->getQuery()->getResult();

        // 2️⃣ Procesar cada trabajador
        $items = [];
        foreach ($trabajadores as $t) {
            $laboral      = $t->getInformacionLaboral();
            $puesto       = $laboral?->getPuesto();
            $categoria    = $laboral?->getCategoria();
            $fechaIngreso = $laboral?->getFechaIncorporacion();

            // === ANTIGÜEDAD ===
            $antiguedadTexto = 'Sin registro';
            $puntosAntiguedad = 0;
            if ($fechaIngreso instanceof \DateTimeInterface) {
                $diff = (new \DateTime())->diff($fechaIngreso);
                $puntosAntiguedad = $diff->y;

                $partes = [];
                if ($diff->y > 0) $partes[] = $diff->y . " año" . ($diff->y > 1 ? "s" : "");
                if ($diff->m > 0) $partes[] = $diff->m . " mes" . ($diff->m > 1 ? "es" : "");
                if ($diff->d > 0) $partes[] = $diff->d . " día" . ($diff->d > 1 ? "s" : "");
                $antiguedadTexto = implode(", ", $partes);
            }

            // === CAPACITACIÓN ===
            $cursosHechosIds = [];
            $puntosCapacitacion = 0;
            foreach ($t->getCapacitacion() as $cap) {
                $curso = $cap->getCurso();
                if ($curso) {
                    $cursosHechosIds[] = $curso->getId();
                    $puntosCapacitacion += $curso->getValor() ?? 0;
                }
            }

            // === SANCIONES ===
            $puntosSancion = 0;
            foreach ($t->getHistorialSanciones() as $s) {
                $puntosSancion += $s->getPuntosSancion() ?? 0;
            }

            // === PUNTAJE TOTAL ===
            $puntajeTotal = $puntosAntiguedad + $puntosCapacitacion - $puntosSancion;

            // === VACANTES QUE CUMPLE ===
            $vacantesCumple = [];
            if ($categoria) {
                $vacantes = $this->vacanteRepo->findBy([
                    'categoria' => $categoria,
                    'activo' => true
                ]);

                foreach ($vacantes as $v) {
                    $cumple = true;
                    foreach ($v->getRequisitos() as $req) {
                        $cursoReq = $req->getCurso();
                        if (!$cursoReq || !in_array($cursoReq->getId(), $cursosHechosIds)) {
                            $cumple = false;
                            break;
                        }
                    }
                    if ($cumple && $v->getVacantesLibres() > 0) {
                        $vacantesCumple[] = $v->getNombre();
                    }
                }
            }

            // Arreglo final del trabajador
            $items[] = [
                'id'                   => $t->getId(),
                'nombre'               => (string)$t,
                'puesto'               => $puesto?->getNombre() ?? 'Sin puesto',
                'categoria'            => $categoria?->getNombre() ?? 'Sin categoría',
                'fecha_ingreso'        => $fechaIngreso?->format('d/m/Y'),
                'antiguedad'           => $antiguedadTexto,
                'puntos_antiguedad'    => $puntosAntiguedad,
                'puntos_capacitacion'  => $puntosCapacitacion,
                'puntos_sancion'       => $puntosSancion,
                'puntaje_total'        => $puntajeTotal,
                'vacantes'             => $vacantesCumple,
            ];
        }

        // 3️⃣ ORDENAMIENTO REAL DEL ESCALAFÓN
        usort($items, function ($a, $b) {

            // 1) Puntaje total DESC
            if ($a['puntaje_total'] !== $b['puntaje_total']) {
                return $b['puntaje_total'] <=> $a['puntaje_total'];
            }

            // 2) Antigüedad DESC
            if ($a['puntos_antiguedad'] !== $b['puntos_antiguedad']) {
                return $b['puntos_antiguedad'] <=> $a['puntos_antiguedad'];
            }

            // 3) Orden alfabético
            return strcmp($a['nombre'], $b['nombre']);
        });

        // 4️⃣ APLICAR PAGINACIÓN AL ARRAY ORDENADO
        $total = count($items);
        $itemsPaginados = array_slice($items, ($page - 1) * $perPage, $perPage);

        return [
            'items'       => $itemsPaginados,
            'total'       => $total,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    // ----------------------------------------------------------------------

    public function getDetalleEscalafon(InformacionPersonal $t): array
    {
        // (este método lo dejé intacto)
        // No afecta el ordenamiento
        $laboral      = $t->getInformacionLaboral();
        $puesto       = $laboral?->getPuesto();
        $categoria    = $laboral?->getCategoria();
        $fechaIngreso = $laboral?->getFechaIncorporacion();

        $antiguedad = 'Sin registro';
        if ($fechaIngreso instanceof \DateTimeInterface) {
            $diff = (new \DateTime())->diff($fechaIngreso);
            $antiguedad = sprintf('%d años, %d meses', $diff->y, $diff->m);
        }

        $cursosHechosIds = [];
        $capacitaciones  = [];
        foreach ($t->getCapacitacion() as $cap) {
            $curso = $cap->getCurso();
            if ($curso) {
                $cursosHechosIds[] = $curso->getId();
                $capacitaciones[]   = [
                    'nombre' => $curso->getNombre(),
                    'valor'  => $curso->getValor(),
                ];
            }
        }

        $sanciones = [];
        foreach ($t->getHistorialSanciones() as $s) {
            $sanciones[] = [
                'fecha'  => $s->getFecha()?->format('d/m/Y'),
                'motivo' => $s->getMotivo(),
                'puntos' => $s->getPuntosSancion(),
            ];
        }

        $ascensos = [];
        foreach ($t->getHistorialAscensos() as $a) {
            $ascensos[] = [
                'fecha'           => $a->getFecha()?->format('d/m/Y'),
                'puesto_anterior' => $a->getPuestoAnterior(),
                'puesto_ascenso'  => $a->getPuestoAscenso(),
            ];
        }

        // Vacantes
        $vacantesDisponibles  = [];
        $vacantesNoElegibles  = [];
        $vacantes             = $categoria ? $this->vacanteRepo->findBy(['categoria' => $categoria]) : [];

        foreach ($vacantes as $v) {
            if (!$v->isActivo()) continue;

            $detalleReq = [];
            $faltantes  = [];
            $cumpleTodo = true;

            foreach ($v->getRequisitos() as $req) {
                $curso = $req->getCurso();
                if (!$curso) {
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
                $vacantesDisponibles[] = $vacInfoBase;
            } else {
                $vacantesNoElegibles[] = $vacInfoBase + [
                    'faltantes' => $faltantes
                ];
            }
        }

        usort($vacantesDisponibles, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
        usort($vacantesNoElegibles, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));

        return [
            'trabajador'    => (string)$t,
            'trabajador_id' => $t->getId(),
            'puesto'        => $puesto?->getNombre() ?? 'Sin puesto',
            'categoria'     => $categoria?->getNombre() ?? 'Sin categoría',
            'antiguedad'    => $antiguedad,

            'capacitaciones'       => $capacitaciones,
            'sanciones'            => $sanciones,
            'ascensos'             => $ascensos,
            'vacantes_disponibles' => $vacantesDisponibles,
            'vacantes_no_elegibles'=> $vacantesNoElegibles,
        ];
    }
}

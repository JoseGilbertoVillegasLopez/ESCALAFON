<?php

namespace App\Service;

use App\Repository\InformacionPersonalRepository;
use App\Repository\VacantesRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    // 🔍 Filtro por nombre (independiente)
    if (!empty($nombre)) {
        $qb->andWhere('
            p.nombre LIKE :q
            OR p.apellidoPaterno LIKE :q
            OR p.apellidoMaterno LIKE :q
        ')
        ->setParameter('q', '%' . $nombre . '%');
    }

    // 🧱 Filtro por categoría (independiente)
    if (!empty($categoriaId)) {
        $qb->andWhere('c.id = :categoria')
           ->setParameter('categoria', $categoriaId);
    }

    // 👉 (opcional) orden por apellidos+nombre para consistencia
    $qb->addOrderBy('p.apellidoPaterno', 'ASC')
       ->addOrderBy('p.apellidoMaterno', 'ASC')
       ->addOrderBy('p.nombre', 'ASC');

    // 🔹 Conteo total (antes de limitar)
    $countQb = clone $qb;
    $total = (int) $countQb->resetDQLPart('select')->select('COUNT(p.id)')
        ->setFirstResult(null)->setMaxResults(null)
        ->getQuery()->getSingleScalarResult();

    // 🔹 Paginación
    $qb->setFirstResult(($page - 1) * $perPage)
       ->setMaxResults($perPage);

    $trabajadores = $qb->getQuery()->getResult();

    // 🔹 Procesamiento
    $items = [];
    foreach ($trabajadores as $t) {
        $laboral = $t->getInformacionLaboral();
        $puesto  = $laboral?->getPuesto();
        $categoria = $laboral?->getCategoria();
        $fechaIngreso = $laboral?->getFechaIncorporacion();

        // 🧮 Antigüedad
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

        // 🎓 Puntos por capacitación
        $puntosCapacitacion = 0;
        foreach ($t->getCapacitacion() as $cap) {
            $curso = $cap->getCurso();
            if ($curso) $puntosCapacitacion += $curso->getValor() ?? 0;
        }

        // ⚠️ Puntos de sanciones
        $puntosSancion = 0;
        foreach ($t->getHistorialSanciones() as $sancion) {
            $puntosSancion += $sancion->getPuntosSancion() ?? 0;
        }

        // 🧮 Puntaje total
        $puntajeTotal = $puntosAntiguedad + $puntosCapacitacion - $puntosSancion;

        // 🧩 Vacantes compatibles
        $vacantesCompatibles = [];
        $vacantes = $this->vacanteRepo->findBy(['categoria' => $categoria]);
        foreach ($vacantes as $v) {
            if ($v->isActivo() && $v->getVacantesLibres() > 0) {
                $vacantesCompatibles[] = $v->getNombre();
            }
        }

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
            'vacantes' => $vacantesCompatibles,
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
}

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
    /* ======================================================
       CONSULTA BASE
       ====================================================== */
    $qb = $this->em->createQueryBuilder()
        ->select('p', 'il', 'pu', 'c')
        ->from('App\Entity\InformacionPersonal', 'p')
        ->leftJoin('p.informacionLaboral', 'il')
        ->leftJoin('il.puesto', 'pu')
        ->leftJoin('il.categoria', 'c');

    if (!empty($nombre)) {
        $qb->andWhere(
            'p.nombre LIKE :q
             OR p.apellidoPaterno LIKE :q
             OR p.apellidoMaterno LIKE :q'
        )->setParameter('q', '%' . $nombre . '%');
    }

    if (!empty($categoriaId)) {
        $qb->andWhere('c.id = :categoria')
           ->setParameter('categoria', $categoriaId);
    }

    $qb->addOrderBy('p.apellidoPaterno', 'ASC')
       ->addOrderBy('p.apellidoMaterno', 'ASC')
       ->addOrderBy('p.nombre', 'ASC');

    $trabajadores = $qb->getQuery()->getResult();

    /* ======================================================
       ARMADO DE ITEMS
       ====================================================== */
    $items = [];
    $hoy = new \DateTime();
    $yearActual = (int)$hoy->format('Y');

    foreach ($trabajadores as $t) {

        $laboral = $t->getInformacionLaboral();
        $puesto  = $laboral?->getPuesto();
        $categoria = $laboral?->getCategoria();
        $fechaIngreso = $laboral?->getFechaIncorporacion();

        // ANTIGÜEDAD
        $antiguedadTexto = 'Sin registro';
        $puntosAntiguedad = 0;

        if ($fechaIngreso instanceof \DateTimeInterface) {
            $diff = $hoy->diff($fechaIngreso);
            $puntosAntiguedad = $diff->y;

            $partes = [];
            if ($diff->y > 0) $partes[] = $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $partes[] = $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
            if ($diff->d > 0) $partes[] = $diff->d . ' día' . ($diff->d > 1 ? 's' : '');

            $antiguedadTexto = $partes ? implode(', ', $partes) : 'Menos de un día';
        }

        // CAPACITACIÓN
        $cursosHechosIds = [];
        $puntosCapacitacion = 0;

        foreach ($t->getCapacitacion() as $cap) {
            $curso = $cap->getCurso();
            if ($curso) {
                $cursosHechosIds[] = $curso->getId();
                $puntosCapacitacion += $curso->getValor() ?? 0;
            }
        }

        // SANCIONES
        $puntosSancion = 0;
        foreach ($t->getHistorialSanciones() as $s) {
            $puntosSancion += $s->getPuntosSancion() ?? 0;
        }

        // VACANTES
        $vacantesCumple = [];
        if ($categoria) {
            $vacantes = $this->vacanteRepo->findBy([
                'categoria' => $categoria,
                'activo'    => true
            ]);

            foreach ($vacantes as $v) {
                $cumple = true;
                foreach ($v->getRequisitos() as $req) {
                    $curso = $req->getCurso();
                    if (!$curso || !in_array($curso->getId(), $cursosHechosIds, true)) {
                        $cumple = false;
                        break;
                    }
                }

                if ($cumple && $v->getVacantesLibres() > 0) {
                    $vacantesCumple[] = $v->getNombre();
                }
            }
        }

        // 30 DÍAS
        $estado30 = null;
        $penalizado30 = false;

        if ($laboral) {
            $json30 = $laboral->getTrabajo30Dias() ?? [];
            $estado30 = $json30[(string)$yearActual] ?? null;
            $penalizado30 = ($estado30 === false);
        }

        $items[] = [
            'id' => $t->getId(),
            'nombre' => (string)$t,
            'puesto' => $puesto?->getNombre() ?? 'Sin puesto',
            'categoria' => $categoria?->getNombre() ?? 'Sin categoría',
            'fecha_ingreso' => $fechaIngreso?->format('d/m/Y'),
            'antiguedad' => $antiguedadTexto,
            'puntos_capacitacion' => $puntosCapacitacion,
            'puntos_sancion' => $puntosSancion,
            'puntaje_total' => $puntosAntiguedad + $puntosCapacitacion - $puntosSancion,
            'vacantes' => $vacantesCumple,
            'trabajo_30_dias' => $estado30,
            'penalizado_30_dias' => $penalizado30,
        ];
    }

    /* ======================================================
       ORDENAMIENTO BASE
       ====================================================== */
    usort($items, function ($a, $b) {
        if ($b['puntaje_total'] <=> $a['puntaje_total']) {
            return $b['puntaje_total'] <=> $a['puntaje_total'];
        }
        return strcmp($a['nombre'], $b['nombre']);
    });

/* ======================================================
   🚨 PENALIZACIÓN REAL POR PRIORIDAD (CORRECTA)
   ====================================================== */

// guardar posición base
foreach ($items as $i => &$row) {
    $row['_base_pos'] = $i;
}
unset($row);

$lugares = 30;

// ordenar por prioridad calculada
usort($items, function ($a, $b) use ($lugares) {

    $pa = $a['_base_pos'] + ($a['penalizado_30_dias'] ? $lugares : 0);
    $pb = $b['_base_pos'] + ($b['penalizado_30_dias'] ? $lugares : 0);

    // prioridad principal
    if ($pa !== $pb) {
        return $pa <=> $pb;
    }

    // desempate: mantener orden base
    return $a['_base_pos'] <=> $b['_base_pos'];
});

// posiciones finales
foreach ($items as $i => &$row) {
    $row['posicion_final'] = $i + 1;
    unset($row['_base_pos']);
}
unset($row);





    /* ======================================================
       POSICIONES FINALES
       ====================================================== */
    foreach ($items as $i => &$row) {
        $row['posicion_final'] = $i + 1;
    }
    unset($row);

    /* ======================================================
       PAGINACIÓN
       ====================================================== */
    $total = count($items);
    $itemsPaginados = array_slice($items, ($page - 1) * $perPage, $perPage);

    return [
        'items' => $itemsPaginados,
        'total' => $total,
        'total_pages' => (int)ceil($total / $perPage),
    ];
}



    // ----------------------------------------------------------------------

    public function getDetalleEscalafon(InformacionPersonal $t): array
    {
        // Sin cambios…
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

        // Vacantes…
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
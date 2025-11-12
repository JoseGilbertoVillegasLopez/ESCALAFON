<?php

namespace App\Repository;

use App\Entity\InformacionPersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InformacionPersonal>
 */
class InformacionPersonalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformacionPersonal::class);
    }
    public function buscarFiltrado(?string $search, ?string $categoria, ?string $puesto, ?string $antiguedad): array
{
    // 🧭 Query base: traemos la información laboral relacionada
    $qb = $this->createQueryBuilder('i')
        ->leftJoin('i.informacionLaboral', 'l')
        ->leftJoin('l.puesto', 'p')
        ->leftJoin('l.categoria', 'c')
        ->addSelect('l', 'p', 'c');

    // 🔍 Búsqueda por nombre completo o parcial
    if ($search) {
        $qb->andWhere('
            LOWER(i.nombre) LIKE LOWER(:search)
            OR LOWER(i.apellidoPaterno) LIKE LOWER(:search)
            OR LOWER(i.apellidoMaterno) LIKE LOWER(:search)
        ')
        ->setParameter('search', '%' . trim($search) . '%');
    }

    // 🧩 Filtro por categoría (comparando por ID)
    if (!empty($categoria)) {
        $qb->andWhere('c.id = :categoriaId')
           ->setParameter('categoriaId', $categoria);
    }

    // 🧩 Filtro por puesto (comparando por ID)
    if (!empty($puesto)) {
        $qb->andWhere('p.id = :puestoId')
           ->setParameter('puestoId', $puesto);
    }

    // ⏳ Ordenar por fecha de incorporación (antigüedad)
    if ($antiguedad === 'asc') {
        $qb->orderBy('l.fechaIncorporacion', 'ASC');
    } elseif ($antiguedad === 'desc') {
        $qb->orderBy('l.fechaIncorporacion', 'DESC');
    } else {
        $qb->orderBy('i.apellidoPaterno', 'ASC');
    }

    // 🚀 Ejecutar y retornar resultados
    return $qb->getQuery()->getResult();
}

public function findDistinctCategorias(): array
{
    return array_column(
        $this->createQueryBuilder('i')
            ->leftJoin('i.informacionLaboral', 'l')
            ->select('DISTINCT l.categoria')
            ->where('l.categoria IS NOT NULL')
            ->orderBy('l.categoria', 'ASC')
            ->getQuery()
            ->getResult(),
        'categoria'
    );
}

public function findDistinctPuestos(): array
{
    return array_column(
        $this->createQueryBuilder('i')
            ->leftJoin('i.informacionLaboral', 'l')
            ->select('DISTINCT l.puesto')
            ->where('l.puesto IS NOT NULL')
            ->orderBy('l.puesto', 'ASC')
            ->getQuery()
            ->getResult(),
        'puesto'
    );
}

}

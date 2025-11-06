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
    // Crea el QueryBuilder para la entidad principal
    $qb = $this->createQueryBuilder('i')
        ->leftJoin('i.informacionLaboral', 'l') // para acceder a puesto, categoria, etc.
        ->addSelect('l');

    // 🔍 Filtro de búsqueda por nombre completo
    if ($search) {
        $qb->andWhere('
            i.nombre LIKE :search
            OR i.apellidoPaterno LIKE :search
            OR i.apellidoMaterno LIKE :search
        ')
        ->setParameter('search', '%' . $search . '%');
    }

    // 🧩 Filtrar por categoría (desde informacionLaboral)
    if ($categoria) {
        $qb->andWhere('l.categoria = :categoria')
           ->setParameter('categoria', $categoria);
    }

    // 🧩 Filtrar por puesto
    if ($puesto) {
        $qb->andWhere('l.puesto = :puesto')
           ->setParameter('puesto', $puesto);
    }

    // ⏳ Ordenar por antigüedad (fechaIncorporacion)
    if ($antiguedad === 'asc') {
        $qb->orderBy('l.fechaIncorporacion', 'ASC');
    } elseif ($antiguedad === 'desc') {
        $qb->orderBy('l.fechaIncorporacion', 'DESC');
    } else {
        $qb->orderBy('i.apellidoPaterno', 'ASC');
    }

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

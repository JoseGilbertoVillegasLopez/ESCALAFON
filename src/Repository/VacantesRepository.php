<?php

namespace App\Repository;

use App\Entity\Vacantes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vacantes>
 */
class VacantesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacantes::class);
    }
    public function buscarFiltrado(?string $nombre, ?string $puesto, ?string $categoria): array
{
    $qb = $this->createQueryBuilder('v')
        ->leftJoin('v.puesto', 'p')
        ->leftJoin('v.categoria', 'c')
        ->addSelect('p', 'c');

    if ($nombre) {
        $qb->andWhere('v.nombre LIKE :nombre')
           ->setParameter('nombre', '%' . $nombre . '%');
    }

    if ($puesto) {
        $qb->andWhere('p.id = :puesto')
           ->setParameter('puesto', $puesto);
    }

    if ($categoria) {
        $qb->andWhere('c.id = :categoria')
           ->setParameter('categoria', $categoria);
    }

    $qb->orderBy('v.nombre', 'ASC');

    return $qb->getQuery()->getResult();
}


    //    /**
    //     * @return Vacantes[] Returns an array of Vacantes objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vacantes
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

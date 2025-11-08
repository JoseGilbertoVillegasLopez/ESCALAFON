<?php

namespace App\Repository;

use App\Entity\Cursos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cursos>
 */
class CursosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cursos::class);
    }
    public function findByFilters(?string $nombre, ?string $categoria): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.categoria', 'cat')
            ->addSelect('cat')
            ->orderBy('c.nombre', 'ASC');

        if ($nombre) {
            $qb->andWhere('LOWER(c.nombre) LIKE LOWER(:nombre)')
               ->setParameter('nombre', "%$nombre%");
        }

        if ($categoria) {
            $qb->andWhere('cat.id = :categoria')
               ->setParameter('categoria', $categoria);
        }

        return $qb->getQuery()->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categoria>
 */
class CategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categoria::class);
    }
    // src/Repository/CategoriaRepository.php
public function findByNombreParcial(?string $nombre): array
{
    $qb = $this->createQueryBuilder('c');

    if ($nombre) {
        $qb->andWhere('LOWER(c.nombre) LIKE LOWER(:nombre)')
           ->setParameter('nombre', '%' . strtolower($nombre) . '%');
    }

    return $qb->orderBy('c.nombre', 'ASC')
              ->getQuery()
              ->getResult();
}



}

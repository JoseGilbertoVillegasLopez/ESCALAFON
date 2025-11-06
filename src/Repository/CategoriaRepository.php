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
    public function findAllNombres(): array
    {
        $resultados = $this->createQueryBuilder('c')
            ->select('c.nombre AS nombre')
            ->orderBy('c.nombre', 'ASC')
            ->getQuery()
            ->getResult();

        return array_column($resultados, 'nombre');
    }

}

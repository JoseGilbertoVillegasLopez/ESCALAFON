<?php

namespace App\Repository;

use App\Entity\Puesto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Puesto>
 */
class PuestoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Puesto::class);
    }

    public function findAllNombres(): array
    {
        $resultados = $this->createQueryBuilder('p')
            ->select('p.nombre AS nombre')
            ->orderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();

        return array_column($resultados, 'nombre');
    }

    public function findByNombreParcial(?string $nombre): array
{
    $qb = $this->createQueryBuilder('p');

    if ($nombre) {
        $qb->andWhere('p.nombre LIKE :nombre OR p.descripcion LIKE :nombre')
           ->setParameter('nombre', '%' . $nombre . '%');
    }

    return $qb->orderBy('p.nombre', 'ASC')
        ->getQuery()
        ->getResult();
}
}

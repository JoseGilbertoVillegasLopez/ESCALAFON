<?php

namespace App\Repository;

use App\Entity\HistorialSanciones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistorialSanciones>
 */
class HistorialSancionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistorialSanciones::class);
    }
public function findByFilters(?string $nombre, ?\DateTime $desde, ?\DateTime $hasta): array
{
    $qb = $this->createQueryBuilder('h')
        ->leftJoin('h.informacionPersonal', 'p')
        ->addSelect('p')
        ->orderBy('h.fecha', 'DESC');

    if ($nombre) {
        $qb->andWhere('LOWER(CONCAT(p.nombre, \' \', p.apellidoPaterno, \' \', p.apellidoMaterno)) LIKE :nombre')
           ->setParameter('nombre', '%' . strtolower($nombre) . '%');
    }

    if ($desde) {
        $qb->andWhere('h.fecha >= :desde')
           ->setParameter('desde', $desde->format('Y-m-d'));
    }

    if ($hasta) {
        $qb->andWhere('h.fecha <= :hasta')
           ->setParameter('hasta', $hasta->format('Y-m-d'));
    }

    return $qb->getQuery()->getResult();
}

}

<?php

namespace App\Repository;

use App\Entity\InformacionLaboral;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InformacionLaboral>
 */
class InformacionLaboralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformacionLaboral::class);
    }

  public function findDistinctCategorias(): array
    {
        $resultados = $this->createQueryBuilder('l')
            ->leftJoin('l.categoria', 'c')
            ->select('DISTINCT c.nombre AS nombre')
            ->where('c.nombre IS NOT NULL')
            ->orderBy('c.nombre', 'ASC')
            ->getQuery()
            ->getResult();

        return array_column($resultados, 'nombre');
    }

    // 🧩 Lista todos los puestos distintos
    public function findDistinctPuestos(): array
    {
        $resultados = $this->createQueryBuilder('l')
            ->leftJoin('l.puesto', 'p')
            ->select('DISTINCT p.nombre AS nombre')
            ->where('p.nombre IS NOT NULL')
            ->orderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();

        return array_column($resultados, 'nombre');
    }
}

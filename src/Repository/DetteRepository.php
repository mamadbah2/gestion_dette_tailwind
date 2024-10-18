<?php

namespace App\Repository;

use App\Entity\Dette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dette::class);
    }

    public function findByMontantPayé(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.montant = d.montantVerser')
            ->getQuery()
            ->getResult();
    }

    public function findByMontantNonPayé(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.montant > d.montantVerser')
            ->getQuery()
            ->getResult();
    }
}

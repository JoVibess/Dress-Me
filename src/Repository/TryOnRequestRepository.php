<?php

namespace App\Repository;

use App\Entity\TryOnRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TryOnRequest>
 */
class TryOnRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TryOnRequest::class);
    }

    public function findOneByJobIdAndStore(string $jobId, int $storeId): ?TryOnRequest
    {
        return $this->createQueryBuilder('try_on_request')
            ->andWhere('try_on_request.jobId = :jobId')
            ->andWhere('IDENTITY(try_on_request.store) = :storeId')
            ->setParameter('jobId', $jobId)
            ->setParameter('storeId', $storeId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

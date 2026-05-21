<?php

namespace App\Repository;

use App\Entity\ApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiToken>
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    public function findActiveByTokenValue(string $tokenValue): ?ApiToken
    {
        return $this->createQueryBuilder('apiToken')
            ->leftJoin('apiToken.store', 'store')
            ->addSelect('store')
            ->leftJoin('store.user', 'user')
            ->addSelect('user')
            ->andWhere('apiToken.tokenValue = :tokenValue')
            ->andWhere('apiToken.isActive = :isActive')
            ->setParameter('tokenValue', $tokenValue)
            ->setParameter('isActive', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

<?php

namespace App\Repository;

use App\Entity\Star;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Star|null find($id, $lockMode = null, $lockVersion = null)
 * @method Star|null findOneBy(array $criteria, array $orderBy = null)
 * @method Star[]    findAll()
 * @method Star[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StarRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Star::class);
    }

    public function getAll(array $params = [])
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.id');

        if (isset($params['user'])) {
            $qb->andWhere('s.user = :user')
                ->setParameter('user', $params['user']);
        }

        return $qb->getQuery();
    }
}

<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getAll(array $params = [])
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.createdAt', 'DESC');

        if (isset($params['keyword'])) {
            $qb->andWhere('p.name like :keyword')
                ->setParameter('keyword', '%' . $params['keyword'] . '%');
        }

        if (isset($params['manager'])) {
            $qb->andWhere('p.manager = :manager')
                ->setParameter('manager', $params['manager']);
        }

        if (isset($params['categoryId'])) {
            $qb->join('p.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $params['categoryId']);
        }

        return $qb->getQuery();
    }

    public function findLast(int $max)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }
}

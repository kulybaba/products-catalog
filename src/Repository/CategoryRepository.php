<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getAll(array $params = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.position');

        if (isset($params['managerId'])) {
            $qb->join('c.users', 'u')
                ->andWhere('u.id = :managerId')
                ->setParameter('managerId', $params['managerId']);
        }

        return $qb->getQuery();
    }

    public function findLast(int $max)
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }
}

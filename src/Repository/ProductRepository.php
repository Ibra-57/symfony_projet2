<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    private const ALLOWED_SORT_FIELDS = ['name', 'price', 'type', 'stock'];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllOrderedByPriceDesc(): array
    {
        return $this->findAllSorted('price', 'DESC');
    }

    public function findAllSorted(string $sort = 'price', string $direction = 'DESC'): array
    {
        if (!in_array($sort, self::ALLOWED_SORT_FIELDS)) {
            $sort = 'price';
        }

        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        return $this->createQueryBuilder('p')
            ->orderBy('p.' . $sort, $direction)
            ->getQuery()
            ->getResult();
    }
}

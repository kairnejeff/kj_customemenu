<?php

namespace PrestaShop\Module\ProductCarrousel\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProductCarrouselRepository extends EntityRepository
{
    public function getNextPosition($productId){
        /** @var QueryBuilder $qb */
        $qb = $this
            ->createQueryBuilder('c')
            ->select(' count(c.position)')
            ->where("c.product_id=$productId");
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }
    public function getCarousselIds($productId){
        /** @var QueryBuilder $qb */
        $qb = $this
            ->createQueryBuilder('c')
            ->select('c.id')
            ->where("c.product_id=$productId");
        ;
        return $qb->getQuery()->getArrayResult();
    }
}
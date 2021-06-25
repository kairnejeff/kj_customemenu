<?php

namespace PrestaShop\Module\ProductCarrousel\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProductCarrouselRepository extends EntityRepository
{
    public function getNextPosition(){
        /** @var QueryBuilder $qb */
        $qb = $this
            ->createQueryBuilder('c')
            ->select(' MAX(c.position)')
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }
}
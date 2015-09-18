<?php

namespace EMC\TableBundle\Provider;

use Doctrine\ORM\QueryBuilder;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface DataProviderInterface {
    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return QueryResultInterface
     */
    public function find(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig);
    
    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return QueryResultInterface
     */
    public function findAll(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig);
}

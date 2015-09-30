<?php

namespace EMC\TableBundle\Provider;

use Doctrine\ORM\QueryBuilder;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface DataProviderInterface {
    /**
     * This method return QueryResult object containing rows and count.
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return QueryResultInterface
     */
    public function find(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig);
}

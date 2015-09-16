<?php

namespace EMC\TableBundle\Provider;

use Doctrine\ORM\QueryBuilder;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface DataProviderInterface {
    public function setQueryBuilder(QueryBuilder $queryBuilder);
    public function setColumns(array $columns);
    
    public function getData($page, $sort, $limit, $filter);
    public function getTotal($filter);
    public function getAll();
}

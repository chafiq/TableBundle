<?php

namespace EMC\TableBundle\Provider;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface QueryConfigInterface {

    public function getSelect();

    public function getOrderBy();

    public function getFilters();

    public function getLimit();

    public function getPage();

    public function getQuery();

    /**
     * @param array $select
     * @return QueryConfigInterface
     */
    public function setSelect(array $select);

    /**
     * @param array $orderBy
     * @return QueryConfigInterface
     */
    public function setOrderBy(array $orderBy);

    /**
     * @param array $filter
     * @return QueryConfigInterface
     */
    public function setFilters(array $filter);

    /**
     * @param int $limit
     * @return QueryConfigInterface
     */
    public function setLimit($limit);

    /**
     * @param int $page
     * @return QueryConfigInterface
     */
    public function setPage($page);

    /**
     * @param string $query
     * @return QueryConfigInterface
     */
    public function setQuery($query);
}

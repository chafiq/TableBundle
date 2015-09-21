<?php

namespace EMC\TableBundle\Provider;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface QueryConfigInterface {

    /**
     * @return array Query builder select
     */
    public function getSelect();

    /**
     * @return array Query builder orderBy
     */
    public function getOrderBy();

    /**
     * @return array Allowed filters
     */
    public function getFilters();

    /**
     * @return array Max rows per page
     */
    public function getLimit();

    /**
     * @return array Actual page nmber
     */
    public function getPage();

    /**
     * @return array Search sequence
     */
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

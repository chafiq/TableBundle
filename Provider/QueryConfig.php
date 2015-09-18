<?php

namespace EMC\TableBundle\Provider;

/**
 * QueryConfig
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class QueryConfig implements QueryConfigInterface {
    /**
     * @var array
     */
    private $select;
    
    /**
     * @var array
     */
    private $orderBy;
    
    /**
     * @var array
     */
    private $filters;

    /**
     * @var int
     */
    private $limit;
    
    /**
     * @var int
     */
    private $page;
    
    /**
     * @var string
     */
    private $query;
    
    public function getSelect() {
        return $this->select;
    }

    public function getOrderBy() {
        return $this->orderBy;
    }

    public function getFilters() {
        return $this->filters;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function getPage() {
        return $this->page;
    }

    public function getFilter() {
        return $this->filter;
    }
    
    public function getQuery() {
        return $this->query;
    }

    public function setSelect(array $select) {
        $this->select = $select;
        return $this;
    }

    public function setOrderBy(array $orderBy) {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function setFilters(array $filters) {
        $this->filters = $filters;
        return $this;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function setPage($page) {
        $this->page = $page;
        return $this;
    }

    public function setQuery($query) {
        $this->query = $query;
    }


}

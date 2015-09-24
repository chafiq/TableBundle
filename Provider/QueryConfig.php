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
    
    /**
     * {@inheritdoc}
     */
    public function getSelect() {
        return $this->select;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderBy() {
        return $this->orderBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter() {
        return $this->filter;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelect(array $select) {
        $this->select = $select;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderBy(array $orderBy) {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilters(array $filters) {
        $this->filters = $filters;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage($page) {
        $this->page = $page;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery($query) {
        $this->query = $query;
        return $this;
    }


}

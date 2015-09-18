<?php

namespace EMC\TableBundle\Provider;

/**
 * QueryResult
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class QueryResult implements QueryResultInterface {
    /**
     * @var array
     */
    private $rows;
    
    /**
     * @var int
     */
    private $count;
    
    function __construct(array $rows, $count) {
        $this->rows = $rows;
        $this->count = $count;
    }
    
    public function getRows() {
        return $this->rows;
    }

    public function getCount() {
        return $this->count;
    }
}

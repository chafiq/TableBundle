<?php

namespace EMC\TableBundle\Tests\Provider;

use Doctrine\ORM\AbstractQuery;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * QueryMock
 *  
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class QueryMock extends AbstractQuery {
    
    /**
     * @var string
     */
    private $dql;
    
    private $limit;
    
    private $offset;
    
    private $count = 30;
    
    function __construct(ArrayCollection $parameters, $dql, $limit, $offset) {
        $this->parameters = $parameters;    
        $this->dql = $dql;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    
    public function getArrayResult() {
        $row = array_fill(0, substr_count($this->dql, 'AS'), null);
        return array_fill($this->offset, $this->offset + ($this->limit ?: $this->count), $row);
    }

    public function getSingleScalarResult() {
        return $this->count;
    }

    protected function _doExecute() {
        
    }

    public function getSQL() {
        
    }
    
    public function getDQL() {
        return $this->dql;
    }

}

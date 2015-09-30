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

    /**
     * 
     * @return array array(
     *    array(1,2),
     *    array(2,4)
     *    array(3,6),
     *    array(4,8),
     *    array(5,10),
     *    array(6,12),
     *    array(7,14),
     *    array(8,16),
     *    array(9,18),
     *    array(10,20)
     * )
     */
    public function getArrayResult() {
        $row = array_keys(array_fill(0, substr_count($this->dql, 'AS col'), null));
        $rows = array_fill($this->offset, $this->offset + ($this->limit ?: $this->count), $row);
        array_walk($rows, function(&$row, $idx){
            array_walk($row, function(&$value, $key) use ($idx) {
                $value = ($value+1) * ($idx+1);
            });
        });
        return $rows;
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

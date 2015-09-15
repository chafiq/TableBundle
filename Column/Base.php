<?php

namespace EMC\TableBundle\Column;

/**
 * Description of Base
 *
 * @author emc
 */
abstract class Base {
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var array
     */
    protected $params;
    
    /**
     * @var callable|null
     */
    protected $filter;

    /**
     * @param string $name
     * @param array|string|null $params
     */
    function __construct($name, array $params = array()) {
        $this->name = $name;
        
        if (is_string($params)) {
            $params = array($params);
        }
        
        $this->params = $params;
    }
    
    public function getName() {
        return $this->name;
    }

    public function getFilter() {
        return $this->filter;
    }
    
    public function getParams() {
        return $this->params;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setFilter(callable $filter = null) {
        $this->filter = $filter;
        return $this;
    }

    public function setParams(array $params) {
        $this->params = $params;
        return $this;
    }
}

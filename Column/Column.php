<?php

namespace EMC\TableBundle\Column;

/**
 * Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class Column implements ColumnInterface {
    /**
     * @var ColumnTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $options;
    
    function __construct(ColumnTypeInterface $type, array $options = array()) {
        $this->type = $type;
        $this->options = $options;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getType() {
        return $this->type;
    }

    public function getOption($name) {
        if ( !isset($this->options[$name]) ) {
            throw new \InvalidArgumentException;
        }
        
        return $this->options[$name];
    }
}

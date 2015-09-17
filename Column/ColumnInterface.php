<?php

namespace EMC\TableBundle\Column;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnInterface {

    /**
     * @return ColumnTypeInterface
     */
    public function getType();
    
    public function getOption($name);
    
    public function getOptions();
    
}

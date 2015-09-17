<?php

namespace EMC\TableBundle\Table;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableInterface {
    public function getData();
    public function getTotal();
    
    public function getColumns();
    
    /**
     * @return TableTypeInterface
     */
    public function getType();
}

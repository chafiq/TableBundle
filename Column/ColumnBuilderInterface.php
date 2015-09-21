<?php

namespace EMC\TableBundle\Column;

/**
 * ColumnBuilderInterface
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnBuilderInterface {
    
    /**
     * Create column object
     * @return \EMC\TableBundle\Column\ColumnInterface
     */
    public function getColumn();
}

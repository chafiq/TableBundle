<?php

namespace EMC\TableBundle\Table\Column;

/**
 * ColumnBuilderInterface
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnBuilderInterface {
    
    /**
     * Create column object
     * @return ColumnInterface
     */
    public function getColumn();
}

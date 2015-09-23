<?php

namespace EMC\TableBundle\Table\Column;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnFactoryInterface {
    
    /**
     * This method create and return the column builder.
     * @param string $name Column name
     * @param string $type Column type
     * @param int $idx Column index
     * @param array $options Column options
     */
    public function create($name, $type, $idx, array $options = array());
}

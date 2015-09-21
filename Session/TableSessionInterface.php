<?php

namespace EMC\TableBundle\Session;

use EMC\TableBundle\Table\TableTypeInterface;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableSessionInterface {
    
    /**
     * restore table from the session
     * @param string $tableId
     */
    public function restore($tableId);
    
    /**
     * Store the table in the session
     * @param \EMC\TableBundle\Table\TableTypeInterface $type
     * @param array|null $data
     * @param array $options
     */
    public function store(TableTypeInterface $type, array $data = null, array $options = array());
}

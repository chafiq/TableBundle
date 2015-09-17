<?php

namespace EMC\TableBundle\Session;

use EMC\TableBundle\Table\TableTypeInterface;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableSessionInterface {
    public function restore($tableId);
    public function store(TableTypeInterface $type, $data = null, array $options = array());
}

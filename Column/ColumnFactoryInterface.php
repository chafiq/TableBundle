<?php

namespace EMC\TableBundle\Column;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnFactoryInterface {
    public function create($name, $type, $idx, array $options = array());
}

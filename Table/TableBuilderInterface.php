<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableBuilderInterface {
    public function add($name, $type, array $options = array());
    public function handleRequest(Request $request);
    public function getOptions();
    public function getData();
    public function getTable();
    public function getColumns();
}

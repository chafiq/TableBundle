<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;
use EMC\TableBundle\Column\ColumnInterface;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableBuilderInterface {
    public function addColumn(ColumnInterface $column);
    public function handleRequest(Request $request);
    public function getOptions();
    public function getData();
    public function getTable();
}

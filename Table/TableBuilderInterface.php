<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;
use EMC\TableBundle\Column\ColumnInterface;

/**
 *
 * @author emc
 */
interface TableBuilderInterface {
    public function addColumn(ColumnInterface $column);
    public function handleRequest(Request $request);
    public function getOptions();
    public function getData();
    public function getTable();
    public function getUid();
    public function setUid($uid);
}

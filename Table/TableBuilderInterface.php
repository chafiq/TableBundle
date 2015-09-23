<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableBuilderInterface {
    
    /**
     * Add one column to the table
     * @param string $name column name
     * @param string $type column type
     * @param array $options column options
     */
    public function add($name, $type, array $options = array());
    
    /**
     * Extract request params
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function handleRequest(Request $request);
    
    /**
     * @return array table options
     */
    public function getOptions();
    
    /**
     * @return array|null table data
     */
    public function getData();
    
    /**
     * @return TableInterface returns table instance
     */
    public function getTable();
    
    /**
     * @return array table columns ColumnInterface[]
     */
    public function getColumns();
}

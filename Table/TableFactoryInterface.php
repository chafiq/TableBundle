<?php

namespace EMC\TableBundle\Table;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableFactoryInterface {
    
    /**
     * This method create and return an table builder.<br/>
     * @param \EMC\TableBundle\Table\TableTypeInterface $type
     * @param array|null $data table data
     * @param array $options table options
     * @return TableBuilderInterface
     */
    public function create(TableTypeInterface $type, array $data = null, array $options = array(), array $params=array());
    
    /**
     * This method must load the table type passed as a string (class path).<br/>
     * It return the table builder.<br/>
     * @see TableFactoryInterface::create
     * @param string $class
     * @param array|null $data
     * @param array $options
     * @return TableBuilderInterface
     * @throws \InvalidArgumentException
     */
    public function load($class, array $data = null, array $options = array());
    
    /**
     * This method must load the table from the session using $tableId identifier.<br/>
     * @see TableFactoryInterface::create
     * @param string $tableId
     * @param array $params
     */
    public function restore($tableId, array $params=array());
}

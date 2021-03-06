<?php

namespace EMC\TableBundle\Table;

use EMC\TableBundle\Table\Type\TableTypeInterface;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableFactoryInterface {
    
    /**
     * This method create and return an table builder.<br/>
     * @param \EMC\TableBundle\Table\Type\TableTypeInterface $type
     * @param array|null $data table data
     * @param array $options table options
     * @param array $params table params
     * @param int $mode Table mode action
     * @return TableBuilderInterface
     */
    public function create(TableTypeInterface $type, array $data = null, array $options = array(), array $params=array(), $mode);
    
    /**
     * This method must load the table type passed as a string (class path).<br/>
     * It return the table builder.<br/>
     * @see TableFactoryInterface::create
     * @param string $class
     * @param array|null $data
     * @param array $options
     * @param array $params table params
     * @param int $mode Table mode action
     * @return TableBuilderInterface
     * @throws \InvalidArgumentException
     */
    public function load($class, array $data = null, array $options = array(), array $params = array(), $mode);
    
    /**
     * This method must load the table from the session using $tableId identifier.<br/>
     * @see TableFactoryInterface::create
     * @param string $tableId
     * @param array $params
     * @param int $mode Table mode action
     * @return TableBuilderInterface
     */
    public function restore($tableId, array $params=array(), $mode);
}

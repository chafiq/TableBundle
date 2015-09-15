<?php

namespace EMC\TableBundle\Table;

/**
 *
 * @author emc
 */
interface TableFactoryInterface {
    /**
     * 
     * @param \EMC\TableBundle\Table\TableTypeInterface $type
     * @param array $data
     * @param array $options
     * @return TableBuilderInterface
     */
    public function create(TableTypeInterface $type, $data = null, array $options = array());
    
    /**
     * 
     * @param string $class
     * @param array $data
     * @param array $options
     * @return TableBuilderInterface
     */
    public function load($class, $data = null, array $options = array());
    
    public function restore($uid);
    public function store(TableBuilderInterface $type, TableTypeInterface $type);
}

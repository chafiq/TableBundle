<?php

namespace EMC\TableBundle\Table;

use EMC\TableBundle\Provider\QueryResultInterface;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableInterface {

    /**
     * @return TableView Table view object
     */
    public function getView();
    
    /**
     * @return QueryResultInterface
     */
    public function getData();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @return array
     */
    public function getOption($name);

    /**
     * @return array
     */
    public function getColumns();

    /**
     * @return Type\TableTypeInterface
     */
    public function getType();

    /**
     * @param \EMC\TableBundle\Provider\QueryResultInterface $data
     */
    public function setData(QueryResultInterface $data);
}

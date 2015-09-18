<?php

namespace EMC\TableBundle\Table;

use EMC\TableBundle\Provider\QueryResultInterface;

/**
 * Table
 * 
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
final class Table implements TableInterface {

    /**
     * @var TableTypeInterface
     */
    private $type;

    /**
     * @var array ColumnInterface[]
     */
    private $columns;

    /**
     * @var QueryResultInterface
     */
    private $data;

    /**
     * @var array
     */
    private $options;

    function __construct(TableTypeInterface $type, array $columns, array $options = array()) {
        $this->type = $type;
        $this->columns = $columns;
        $this->options = $options;
        $this->data = null;
    }

    public function getView() {
        $view = new TableView();
        $this->type->buildView($view, $this, $this->options);
        return $view;
    }

    /**
     * @return QueryResultInterface
     */
    public function getData() {
        return $this->data;
    }

    public function getType() {
        return $this->type;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setData(QueryResultInterface $data) {
        $this->data = $data;
    }

    public function getOption($name) {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException;
        }

        return $this->options[$name];
    }

}

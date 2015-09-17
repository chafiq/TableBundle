<?php

namespace EMC\TableBundle\Table;

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
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $total;

    /**
     * @var array
     */
    private $options;

    function __construct(TableTypeInterface $type, array $columns, $data, $total, array $options = array()) {
        $this->type = $type;
        $this->columns = $columns;
        $this->data = $data;
        $this->total = $total;
        $this->options = $options;
    }

    public function getView() {
        $view = new TableView();
        $this->type->buildView($view, $this, $this->options);
        return $view;
    }

    public function getData() {
        return $this->data;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getType() {
        return $this->type;
    }

    public function getColumns() {
        return $this->columns;
    }

}

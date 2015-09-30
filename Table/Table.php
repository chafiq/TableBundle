<?php

namespace EMC\TableBundle\Table;

use EMC\TableBundle\Provider\QueryResultInterface;
use EMC\TableBundle\Table\Type\TableTypeInterface;

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

    /**
     * {@inheritdoc}
     */
    public function getView() {
        $view = new TableView();
        $this->type->buildView($view, $this);

        return $view;
    }

    /**
     * @return QueryResultInterface
     */
    public function getData() {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(QueryResultInterface $data) {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name) {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException('Unknown option name "' . $name . '"');
        }

        return $this->options[$name];
    }

    public function export($type) {

        if (!is_string($type)) {
            throw new \InvalidArgumentException('$type string is required');
        }

        $exports = $this->getOption('export');
        if (!isset($exports[$type])) {
            throw new \UnexpectedValueException('Export type "' . $type . '" not available');
        }

        /* @var $export \EMC\TableBundle\Table\Export\Extension\ExportExtensionInterface */
        $export = $exports[$type];

        return $export->export($this->getView());
    }

}

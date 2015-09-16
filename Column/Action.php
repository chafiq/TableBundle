<?php

namespace EMC\TableBundle\Column;

/**
 * Column Action Container
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class Action extends Base implements ColumnInterface, ActionInterface {
    
    /**
     * @var array
     */
    protected $columns;
    
    function __construct() {
        parent::__construct('_action');
    }
    
    public function getColumns() {
        return $this->columns;
    }
    
    public function addColumn(ColumnInterface $column) {
        $this->columns[] = $column;
        return $this;
    }

    public function getExtension() {
        return 'action';
    }

    public function getView($data) {
        return array(
            'columns' => $this->columns,
            'data' => $data
        );
    }

    public function format(array $data) {
        return $data;
    }

}

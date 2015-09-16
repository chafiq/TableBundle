<?php

namespace EMC\TableBundle\Table;

use EMC\TableBundle\Column\TextInterface;
use EMC\TableBundle\Column\ActionInterface;
use EMC\TableBundle\Column\ColumnInterface;

final class Table implements TableInterface {

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $caption;
    
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
    private $columns;
    
    /**
     * @var array
     */
    private $query;

    function __construct($id, $name, $caption, $columns, $data, $total, array $query) {
        $this->id = $id;
        $this->name = $name;
        $this->caption = $caption;
        $this->columns = $columns;
        $this->data = $data;
        $this->total = $total;
        $this->query = $query;
    }

    public function getView() {
        return array_merge($this->query, array(
            'id'    => $this->id,
            'domId' => 'table_' . $this->name,
            'caption'=> $this->caption,
            'columns'=> $this->columns,
            'thead' => $this->getHeader(),
            'tbody' => $this->getBody(),
            'tfoot' => $this->getFooter(),
            'filter'=> $this->hasFilter(),
            'total' => $this->total
        ));
    }
    
    protected function getHeader() {
        $header = array();

        foreach ($this->columns as $idx => $column) {
            if ($column instanceof TextInterface) {
                $header['col' . $idx] = array(
                    'title' => $column->getName(),
                    'sort' => $column->isSortable() ? $idx + 1 : 0
                );
            }
        }

        foreach ($this->columns as $idx => $column) {
            if ($column instanceof ActionInterface) {
                $header['actions'] = array('title' => '', 'sort' => 0);
                break;
            }
        }

        return $header;
    }

    protected function getFooter() {
        return array();
        $footer = array();
        foreach ($this->columns as $idx => $column) {
            if (is_string($column['footer'])) {
                $footer['col' . $idx] = $column['footer'];
            } else if (is_callable($column['footer'])) {
                $footer['col' . $idx] = call_user_func($column['footer'], $this->extract($column, $this->data));
            } else {
                throw new \InvalidArgumentException('$column[footer] string|function expected');
            }
        }
        return $footer;
    }

    protected function getBody() {

        $rows = array_fill(0, count($this->data), array());

        foreach ($this->columns as $idx => $column) {
            $_data = $this->extract($column);
            foreach ($_data as $row => $__data) {
                $rows[$row][$idx] = $__data;
            }
        }
        
        return $rows;
    }

    protected function hasFilter() {
        foreach($this->columns as $column) {
            if ( $column instanceof TextInterface && $column->isSearchable() ) {
                return true;
            }
        }
        return false;
    }
    
    private function extract(ColumnInterface $column) {
        $result = array();
        foreach ($this->data as $_data) {

            $__data = array();
            foreach ($column->getParams() as $_column) {
                $__data[$_column] = $_data[$_column];
            }
            $result[] = $__data;
        }
        return $result;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCaption() {
        return $this->caption;
    }

    public function getData() {
        return $this->data;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getQuery() {
        return $this->query;
    }


}

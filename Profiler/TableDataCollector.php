<?php

namespace EMC\TableBundle\Profiler;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EMC\TableBundle\Table\TableInterface;
use EMC\TableBundle\Table\TableTypeInterface;
use EMC\TableBundle\Column\ActionInterface;

/**
 * Description of TableDataCollector
 *
 * @author emc
 */
class TableDataCollector extends DataCollector {
    public function collect(Request $request, Response $response, \Exception $exception = null) {
    }
    
    public function collectConfig(TableInterface $table, $data = null, array $options = array()) {
        $this->init();
        $this->data['tables'][$table->getId()] = array(
            'name'      => $table->getName(),
            'options'   => $options,
            'caption'   => $table->getCaption(),
            'total'     => $table->getTotal(),
            'query'     => $table->getQuery(),
            'columns'   => $this->getTableColumns($table->getColumns())
        );
    }
    
    protected function init() {
        if ( isset($this->data['tables']) ) {
            return;
        }
        
        $this->data = array(
            'tables' => array()
        );
    }
    
    protected function getTableColumns(array $columns) {
        $config = array();
        
        /* @var $column \EMC\TableBundle\Column\ColumnInterface */
        $column = null;
        foreach($columns as $column) {
            if ( $column instanceof ActionInterface ) {
                $config = array_merge($config, $this->getTableColumns($column->getColumns()));
            } else {
                $config[$column->getName()] = array(
                    'class' => get_class($column),
                    'params' => $column->getParams(),
                    'type'  => $column->getExtension()
                );
            }
        }
        
        return $config;
    }

    public function getTables() {
        return isset($this->data['tables']) ? $this->data['tables'] : array();
    }
    
    public function getName() {
        return 'table';
    }
}

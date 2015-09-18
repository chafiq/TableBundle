<?php

namespace EMC\TableBundle\Profiler\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EMC\TableBundle\Table\TableInterface;
use EMC\TableBundle\Column\ActionInterface;

/**
 * TableDataCollector
 * 
 * This class collect data for the WebProfiler
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableDataCollector extends DataCollector {
    public function collect(Request $request, Response $response, \Exception $exception = null) {
    }
    
    public function collectConfig(TableInterface $table, $data = null, array $options = array()) {
        
        if ( !isset($options['_tid']) ) {
            throw new \RuntimeException;
        }
        
        $this->init();
        $this->data['tables'][$options['_tid']] = array(
            'name'      => $options['name'],
            'options'   => $options,
            'caption'   => $options['caption'],
            'columns'   => $this->getTableColumns($table->getColumns()),
            'total'     => -1,
            'query'     => array()
        );
    }
    
    
    public function collectData(TableInterface $table, $data = null, array $options = array()) {
        
        if ( !isset($options['_tid']) ) {
            throw new \RuntimeException;
        }
        
        $this->init();
        
        if ( !isset($this->data['tables'][$options['_tid']]) ) {
            $this->collectConfig($table, $data, $options);
        }
        
        $this->data['tables'][$options['_tid']]['total'] = $table->getData()->getCount();
        $this->data['tables'][$options['_tid']]['query'] = $options['_query'];
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
                $config[$column->getOption('name')] = array(
                    'params'=> $column->getOption('params'),
                    'type'  => $column->getType()->getName(),
                    'class' => get_class($column->getType())
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

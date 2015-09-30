<?php

namespace EMC\TableBundle\Table\Type\Decorator;

use EMC\TableBundle\Provider\QueryConfigInterface;
use EMC\TableBundle\Table\TableInterface;
use EMC\TableBundle\Table\TableView;

/**
 * TableExportDecorator
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableSelectionDecorator extends TableDecorator {
    public function buildView(TableView $view, TableInterface $table) {
        $data = array();
        
        foreach ($table->getData()->getRows() as $_data) {
            $row = $this->resolveParams($table->getOption('rows_params'), $_data, true);
            $data['row_' . implode('_', $row)] = $row;
        }
        
        $view->setData($data);
    }
    
    public function buildQuery(QueryConfigInterface $query, TableInterface $table) {
        parent::buildQuery($query, $table);
        $params = $table->getOption('rows_params');
        $query->setSelect(array_values($params));
        $query->setLimit(0);
        $query->setPage(1);
    }
}

<?php

namespace EMC\TableBundle\Table\Type\Decorator;

use EMC\TableBundle\Table\TableInterface;
use EMC\TableBundle\Provider\QueryConfigInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * TableExportDecorator
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableExportDecorator extends TableDecorator {

    public function buildHeaderCellView(array &$view, ColumnInterface $column) {
        if (!$column->getType()->isExportable()) {
            $view = null;
            return;
        }
        return parent::buildHeaderCellView($view, $column);
    }

    public function buildBodyCellView(array &$view, ColumnInterface $column, array $data) {
        if (!$column->getType()->isExportable()) {
            $view = null;
            return;
        }
        return parent::buildBodyCellView($view, $column, $data);
    }

    public function buildQuery(QueryConfigInterface $query, TableInterface $table) {
        parent::buildQuery($query, $table);

        $_query = $table->getOption('_query');
        $params = $table->getOption('rows_params');
        
        if ($table->getOption('allow_select')) {
            
            if ( !isset($_query['selectedRows']) ) {
                throw new \InvalidArgumentException('selectedRows is required');
            }
            
            if (count($_query['selectedRows']) === 0) {
                $query->setValid(false);
                return;
            }
            
            if (count($params) === 1) {
                $name = key($params);
                $param = reset($params);

                $selectedRowIds = array();
                foreach ($_query['selectedRows'] as $row) {
                    if (!isset($row[$name])) {
                        throw new \UnexpectedValueException('Parameter "' . $name . '" not found.');
                    }
                    $selectedRowIds[] = $row[$name];
                }

                $orX = new \Doctrine\ORM\Query\Expr\Orx();
                $orX->add($param . ' in (:selectedRowIds)');
                $query->setConstraints($orX)
                        ->addParameter('selectedRowIds', $selectedRowIds);
            } else {
                $reversedParams = array_flip($params);
                if (count($params) !== count($reversedParams)) {
                    throw new \RuntimeException('rows_params values must be unique');
                }

                $query->setParameters(array());

                $orX = new \Doctrine\ORM\Query\Expr\Orx();
                $selectedRows = array_values($_query['selectedRows']);
                foreach ($selectedRows as $i => $row) {
                    $row = $this->type->resolveParams($reversedParams, $row);
                    $andX = new \Doctrine\ORM\Query\Expr\Andx();
                    foreach ($row as $param => $value) {
                        $andX->add($param . ' = :' . $reversedParams[$param] . $i);
                        $query->addParameter($reversedParams[$param] . $i, $value);
                    }
                    $orX->add($andX);
                }

                $query->setConstraints($orX);
            }
        }
    }

}

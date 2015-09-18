<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Column\ColumnInterface;
use EMC\TableBundle\Provider\DataProvider;
use EMC\TableBundle\Provider\QueryConfigInterface;

/**
 * TableType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class TableType implements TableTypeInterface {

    public function buildTable(TableBuilderInterface $builder, array $options) {
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(array(
            'name' => $this->getName(),
            'route' => '_table',
            'data' => null,
            'params'=> array(),
            'attrs'=> array(),
            'data_provider' => new DataProvider(),
            'default_sorts' => array(),
            'limit' => 10,
            'selector' => false,
            'caption' => '',
            'route' => '_table',
            'subtable' => null,
            'subtable_options' => array(),
            'subtable_params' => array()
        ));

        $resolver->setAllowedTypes(array(
            'name' => 'string',
            'route' => 'string',
            'data' => array('null', 'array'),
            'params' => 'array',
            'attrs' => 'array',
            'data_provider' => array('null', 'EMC\TableBundle\Provider\DataProviderInterface'),
            'default_sorts' => 'array',
            'limit' => 'int',
            'selector' => 'bool',
            'caption' => 'string',
            'route' => 'string',
            'subtable' => array('null', 'EMC\TableBundle\Table\TableTypeInterface'),
            'subtable_options' => 'array',
            'subtable_params' => 'array'
        ));
    }

    public function buildView(TableView $view, TableInterface $table, array $options = array()) {

        if (!isset($options['_tid'])) {
            throw new \RuntimeException;
        }

        if ( !isset($options['attrs']['id']) ) {
            $options['attrs']['id'] = 'table_' . $table->getType()->getName();
        }
        
        $view->setData(array(
            'id' => $options['_tid'],
            'subtid' => isset($options['_subtid']) ? $options['_subtid'] : null,
            'params' => $options['params'],
            'attrs' => $options['attrs'],
            'caption' => $options['caption'],
            'thead' => $this->buildHeaderView($table),
            'tbody' => $this->buildBodyView($table),
            'tfoot' => $this->buildFooterView($table),
            'total' => $table->getData()->getCount(),
            'subtable' => $this->buildSubtableParams($table, $options),
            'limit' => isset($options['_query']['limit']) ? $options['_query']['limit'] : $options['limit'],
            'page' => isset($options['_query']['page']) ? $options['_query']['page'] : 1,
            'has_filter' => $this->hasFilter($table),
            'route' => $options['route']
        ));
    }

    protected function buildHeaderView(TableInterface $table) {
        $view = array();

        /* @var $column \EMC\TableBundle\Column\ColumnInterface */
        $column = null;
        foreach ($table->getColumns() as $name => $column) {
            $_view = array();
            $column->getType()->buildHeaderView($_view, $column);
            $view[$name] = $_view;
        }

        return $view;
    }

    protected function buildFooterView(TableInterface $table) {
        
    }

    protected function buildBodyView(TableInterface $table) {

        if (($count = count($table->getData())) === 0) {
            return array();
        }

        $view = array();

        foreach ($table->getData()->getRows() as $_data) {
            $rowView = array();
            foreach ($table->getColumns() as $name => $column) {
                $__data = $this->extract($column->getOption('params'), $_data);
                $cellView = array();
                $column->getType()->buildView($cellView, $column, $__data, $column->getOptions());
                $column->getType()->buildCellView($cellView, $column, $__data);
                $rowView[$name] = $cellView;
            }
            $view[] = $rowView;
        }

        return $view;
    }

    public function buildQuery(QueryConfigInterface $query, TableInterface $table, array $options = array()) {

        $select = array();
        $filters = array();
        $orderBy = array();

        if (count($options['subtable_params']) > 0) {
            $select = array_merge($select, $options['subtable_params']);
        }

        $filter = $options['_query']['filter'];

        if (strlen($filter) > 0 && strlen($filter) < 3) {
            throw new \InvalidArgumentException;
        }

        /* @var $column ColumnInterface */
        $column = null;
        $columns = $table->getColumns();
        foreach ($columns as $column) {
            $params = $column->getOption('params');
            if (count($params) > 0) {
                $select = array_merge($select, $params);
            }

            $allowFilter = $column->resolveAllowedParams('allow_filter');
            if ($allowFilter !== null) {
                $filters = array_merge($filters, $allowFilter);
            }
        }

        $sort = abs($options['_query']['sort']);
        if ($sort !== 0 && isset($columns[$sort])) {

            $allowSort = $columns[$sort]->resolveAllowedParams('allow_sort');
            if ($allowSort !== null) {
                $orderBy = array_fill_keys(
                        $allowSort, $options['_query']['sort'] > 0
                );
            }
        }

        $query->setSelect(array_unique(array_values($select)))
                ->setOrderBy($orderBy)
                ->setFilters($filters)
                ->setLimit($options['_query']['limit'])
                ->setPage($options['_query']['page'])
                ->setQuery($options['_query']['filter']);
    }

    private function buildSubtableParams(TableInterface $table, array $options) {
        if (!$options['subtable'] || count($options['subtable_params']) === 0) {
            return null;
        }
        $subtableParams = array();
        foreach ($table->getData()->getRows() as $row) {
            $subtableParams[] = $this->extract($options['subtable_params'], $row, true);
        }
        return $subtableParams;
    }

    protected function hasFilter(TableInterface $table) {
        foreach ($table->getColumns() as $column) {
            $options = $column->getOptions();
            if ($options['allow_filter']) {
                return true;
            }
        }
        return false;
    }

    private function extract(array $params, array $data, $preserveKeys = false) {
        $result = array();
        foreach ($params as $key => $param) {
            $result[$preserveKeys ? $key : $param] = $data[$param];
        }
        return $result;
    }

}

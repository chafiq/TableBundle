<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Column\ColumnInterface;
use EMC\TableBundle\Provider\DataProvider;
use EMC\TableBundle\Provider\QueryConfigInterface;

/**
 * TableType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class TableType implements TableTypeInterface {

    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $builder, array $options) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(array(
            'name' => $this->getName(),
            'route' => '_table',
            'data' => null,
            'params' => array(),
            'attrs' => array(),
            'data_provider' => new DataProvider(),
            'default_sorts' => array(),
            'limit' => 10,
            'caption' => '',
            'route' => '_table',
            'subtable' => null,
            'subtable_options' => array(),
            'subtable_params' => array(),
            'rows_pad' => true,
            'rows_params' => array(),
            'allow_select' => false
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
            'caption' => 'string',
            'route' => 'string',
            'subtable' => array('null', 'EMC\TableBundle\Table\TableTypeInterface'),
            'subtable_options' => 'array',
            'subtable_params' => 'array',
            'rows_pad' => 'bool',
            'rows_params' => 'array',
            'allow_select' => 'bool',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(TableView $view, TableInterface $table, array $options = array()) {

        if (!isset($options['_tid'])) {
            throw new \RuntimeException;
        }

        if (!isset($options['attrs']['id'])) {
            $options['attrs']['id'] = 'table_'
                    . $table->getType()->getName()
                    . (count($options['params']) > 0 ? '_' . implode('_', $options['params']) : '' );
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
            'limit' => isset($options['_query']['limit']) ? $options['_query']['limit'] : $options['limit'],
            'page' => isset($options['_query']['page']) ? $options['_query']['page'] : 1,
            'has_filter' => $this->hasFilter($table),
            'route' => $options['route'],
            'rows_pad' => $options['rows_pad'],
            'allow_select' => $options['allow_select']
        ));
    }

    /**
     * Build the header view.
     * @param \EMC\TableBundle\Table\TableInterface $table
     * @return array
     */
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

    /**
     * Build the footer view.
     * @todo implement table footer
     * @param \EMC\TableBundle\Table\TableInterface $table
     * @return array
     */
    protected function buildFooterView(TableInterface $table) {
        return array();
    }

    /**
     * Build the body view.
     * @param \EMC\TableBundle\Table\TableInterface $table
     * @return array
     */
    protected function buildBodyView(TableInterface $table) {

        if (($count = count($table->getData())) === 0) {
            return array();
        }

        $view = array();

        foreach ($table->getData()->getRows() as $_data) {
            $rowView = array();
            foreach ($table->getColumns() as $name => $column) {
                $__data = $this->resolveParams($column->getOption('params'), $_data);
                $cellView = array();
                $column->getType()->buildView($cellView, $column, $__data, $column->getOptions());
                $column->getType()->buildCellView($cellView, $column, $__data);
                $rowView[$name] = $cellView;
            }
            
            $view[] = array(
                'params'    => $this->resolveParams($table->getOption('rows_params'), $_data, true),
                'subtable'  =>  $table->getOption('subtable') instanceof TableTypeInterface ?
                                $this->resolveParams($table->getOption('subtable_params'), $_data, true, null) :
                                null,
                'data'      => $rowView
            );
        }

        return $view;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder(ObjectManager $entityManager, array $params) {
        return null;
    }

    /**
     * This method return TRUE if one or more columns allow filtering.
     * 
     * @param \EMC\TableBundle\Table\TableInterface $table
     * @return boolean
     */
    protected function hasFilter(TableInterface $table) {
        foreach ($table->getColumns() as $column) {
            $options = $column->getOptions();
            if ($options['allow_filter']) {
                return true;
            }
        }
        return false;
    }

    /**
     * This method populate $params with values in $data.<br/>
     * 
     * @param array $params
     * @param array $data
     * @param bool $preserveKeys    Preserve or not the $params keys. Default false
     * @param null|array $default   Returned if $params is empty
     * @return array
     * @throws \RuntimeException
     */
    private function resolveParams(array $params, array $data, $preserveKeys = false, $default = array()) {
        if (count($params) === 0) {
            return $default;
        }

        $result = array();
        foreach ($params as $key => $param) {
            if (!isset($data[$param])) {
                throw new \RuntimeException('Unknown parameter "' . $param . '"');
            }
            $result[$preserveKeys ? $key : $param] = $data[$param];
        }
        return $result;
    }

}

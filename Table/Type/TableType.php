<?php

namespace EMC\TableBundle\Table\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Table\Column\ColumnInterface;
use EMC\TableBundle\Provider\DataProviderInterface;
use EMC\TableBundle\Provider\QueryConfigInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Table\TableBuilderInterface;
use EMC\TableBundle\Table\TableInterface;
use EMC\TableBundle\Table\TableView;

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
    public function setDefaultOptions(OptionsResolverInterface $resolver, array $defaultOptions) {
        
        if (!class_exists($defaultOptions['data_provider'])) {
            throw new \UnexpectedValueException('data_provider must a valid class name');
        }
        $dataProvider = new $defaultOptions['data_provider'];
        if (!$dataProvider instanceof DataProviderInterface) {
            throw new \InvalidArgumentException('data_provider must a string class name that implements \EMC\TableBundle\Provider\DataProviderInterface');
        }
        
        $resolver->setDefaults(array(
            'name' => $this->getName(),
            'route' => $defaultOptions['route'],
            'data' => null,
            'params' => array(),
            'attrs' => array(),
            'data_provider' => $dataProvider,
            'default_sorts' => array(),
            'limit' => $defaultOptions['limit'],
            'caption' => '',
            'subtable' => null,
            'subtable_options' => array(),
            'subtable_params' => array(),
            'rows_pad' => $defaultOptions['rows_pad'],
            'rows_params' => array(),
            'allow_select' => false,
            'select_route' => $defaultOptions['select_route'],
            'export' => array(),
            'export_route' => $defaultOptions['export_route'],
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
            'subtable' => array('null', 'EMC\TableBundle\Table\Type\TableTypeInterface'),
            'subtable_options' => 'array',
            'subtable_params' => 'array',
            'rows_pad' => 'bool',
            'rows_params' => 'array',
            'allow_select' => 'bool',
            'select_route' => 'string',
            'export' => 'array',
            'export_route' => 'string',
        ));

        $resolver->setNormalizers(array(
            'allow_select' => function($options, $allowSelect) {
        if ($allowSelect && count($options['rows_params']) === 0) {
            throw new \InvalidArgumentException('rows_params is required if allow_select is true');
        }
        return $allowSelect;
    }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(TableView $view, TableInterface $table) {

        $options = $table->getOptions();

        if (!isset($options['_tid'])) {
            throw new \RuntimeException;
        }

        if (!isset($options['attrs']['id'])) {
            $options['attrs']['id'] = 'table_'
                    . $table->getType()->getName()
                    . (count($options['params']) > 0 ? '_' . implode('_', $options['params']) : '' );
        }

        $data = array(
            'id' => $options['_tid'],
            'subtid' => isset($options['_subtid']) ? $options['_subtid'] : null,
            'params' => $options['params'],
            'attrs' => $options['attrs'],
            'caption' => $options['caption'],
            'thead' => array(),
            'tbody' => array(),
            'tfoot' => array(),
            'total' => $table->getData() !== null ? $table->getData()->getCount() : 0,
            'limit' => $options['_query']['limit'],
            'page' => $options['_query']['page'],
            'has_filter' => $this->hasFilter($table),
            'route' => $options['route'],
            'rows_pad' => $options['rows_pad'],
            'allow_select' => $options['allow_select'],
            'export' => $this->buildExportView($options['export']),
            'export_route' => $options['export_route'],
            'select_route' => $options['select_route']
        );

        $table->getType()->buildHeaderView($data['thead'], $table);
        $table->getType()->buildBodyView($data['tbody'], $table);
        $table->getType()->buildFooterView($data['tfoot'], $table);

        $view->setData($data);
    }

    public function buildHeaderView(array &$view, TableInterface $table) {
        /* @var $column \EMC\TableBundle\Table\Column\ColumnInterface */
        $column = null;
        foreach ($table->getColumns() as $name => $column) {
            $cellView = array();
            $table->getType()->buildHeaderCellView($cellView, $column);
            if ($cellView !== null) {
                $view[$name] = $cellView;
            }
        }
    }

    public function buildBodyView(array &$view, TableInterface $table) {
        if ($table->getData() === null || count($table->getData()->getRows()) === 0) {
            return array();
        }

        foreach ($table->getData()->getRows() as $_data) {
            $rowView = array(
                'params' => $this->resolveParams($table->getOption('rows_params'), $_data, true),
                'subtable' => $table->getOption('subtable') instanceof TableTypeInterface ?
                        $this->resolveParams($table->getOption('subtable_params'), $_data, true, null) :
                        null,
                'data' => array()
            );

            foreach ($table->getColumns() as $name => $column) {
                $cellView = array();
                $table->getType()->buildBodyCellView($cellView, $column, $_data);
                if ($cellView !== null) {
                    $rowView['data'][$name] = $cellView;
                }
            }

            $view[] = $rowView;
        }
    }

    public function buildFooterView(array &$view, TableInterface $table) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeaderCellView(array &$view, ColumnInterface $column) {
        $column->getType()->buildHeaderView($view, $column);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBodyCellView(array &$view, ColumnInterface $column, array $data) {
        $_data = $this->resolveParams($column->getOption('params'), $data);
        $column->getType()->buildView($view, $column, $_data, $column->getOptions());
    }

    protected function buildExportView(array $exports) {
        $view = array();

        /* @var $export \EMC\TableBundle\Table\Export\ExportInterface */
        $export = null;
        foreach ($exports as $export) {
            $view[$export->getName()] = array(
                'text' => $export->getText(),
                'icon' => $export->getIcon()
            );
        }
        return $view;
    }

    /**
     * {@inheritdoc}
     */
    public function buildQuery(QueryConfigInterface $query, TableInterface $table) {

        $select = array();
        $filters = array();
        $orderBy = array();

        $options = $table->getOptions();

        if (count($options['subtable_params']) > 0) {
            $select = array_merge($select, $options['subtable_params']);
        }

        if (count($options['rows_params']) > 0) {

            $select = array_merge($select, $options['rows_params']);
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
        if ($sort > 0) {
            $sort -= 1;
            if ($sort > count($columns)) {
                throw new \UnexpectedValueException('Unkown sort column index ' . $sort);
            }

            $columnNames = array_keys($columns);
            $sortName = $columnNames[$sort];
            unset($columnNames);

            $allowSort = $columns[$sortName]->resolveAllowedParams('allow_sort');
            if ($allowSort === null) {
                throw new \UnexpectedValueException('Column sorting index ' . $sort . ' not allowed');
            }
            $orderBy = array_fill_keys(
                    $allowSort, $options['_query']['sort'] > 0
            );
        }

        $query->setSelect(array_unique(array_values($select)))
                ->setOrderBy($orderBy)
                ->setLimit($options['_query']['limit'])
                ->setPage($options['_query']['page']);

        if (strlen($filter) > 0) {
            foreach ($filters as $col) {
                $query->getConstraints()->add('LOWER(' . $col . ') LIKE :query');
            }
            $query->addParameter('query', '%' . strtolower($filter) . '%');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder(ObjectManager $entityManager, array $params) {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver() {
        return new OptionsResolver();
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
    public function resolveParams(array $params, array $data, $preserveKeys = false, $default = array()) {
        if (count($params) === 0) {
            return $default;
        }

        $result = array();
        foreach ($params as $key => $param) {
            if (!array_key_exists($param, $data)) {
                throw new \RuntimeException('Unknown parameter "' . $param . '"');
            }
            $result[$preserveKeys || is_string($key) ? $key : $param] = $data[$param];
        }
        return $result;
    }

}

<?php

namespace EMC\TableBundle\Table;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Column\ColumnInterface;

/**
 * TableType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class TableType implements TableTypeInterface {

    public function buildTable(TableBuilderInterface $builder, array $options) {
        
    }

    abstract public function getQueryBuilder(ObjectManager $entityManager, array $options);

    abstract public function getName();

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(array(
            'name' => $this->getName(),
            'route' => '_table',
            'data' => null,
            'data_provider' => 'EMC\TableBundle\Provider\DataProvider',
            'default_sorts' => array(),
            'limit' => 10,
            'selector' => false,
            'caption'   => '',
            'route' => '_table'
        ));

        $resolver->setAllowedTypes(array(
            'name' => 'string',
            'route' => 'string',
            'data' => array('null', 'array'),
            'data_provider' => array('null', 'string'),
            'default_sorts' => 'array',
            'limit' => 'int',
            'selector'  => 'bool',
            'caption'   => 'string',
            'route'   => 'string'
        ));
    }

    public function buildView(TableView $view, TableInterface $table, array $options = array()) {

        if (!isset($options['_tid'])) {
            throw new \RuntimeException;
        }

        $view->setData(array(
            'id' => $options['_tid'],
            'domId' => 'table_' . $table->getType()->getName(),
            'caption' => $options['caption'],
            'thead' => $this->buildHeaderView($table),
            'tbody' => $this->buildBodyView($table),
            'tfoot' => $this->buildFooterView($table),
            'total' => $table->getTotal(),
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

        $view = array_fill(0, $count, array());

        foreach ($table->getData() as $_data) {
            $rowView = array();
            foreach ($table->getColumns() as $name => $column) {
                $__data = $this->extract($column, $_data);
                $cellView = array();
                $column->getType()->buildView($cellView, $column, $__data, $column->getOptions());
                $column->getType()->buildCellView($cellView, $column, $__data);
                $rowView[$name] = $cellView;
            }
            $view[] = $rowView;
        }

        return $view;
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

    private function extract(ColumnInterface $column, array $data) {
        $result = array();
        $options = $column->getOptions();
        foreach ($options['params'] as $param) {
            $result[$param] = $data[$param];
        }
        return $result;
    }

}

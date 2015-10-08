<?php

namespace EMC\TableBundle\Table\Type\Decorator;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Table\Type\TableTypeInterface;
use EMC\TableBundle\Provider\QueryConfigInterface;
use EMC\TableBundle\Table\TableInterface;
use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\TableBuilderInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * TableExportDecorator
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class TableDecorator implements TableTypeInterface, TableDecoratorInterface {

    /**
     * @var \EMC\TableBundle\Table\Type\TableTypeInterface
     */
    protected $type;

    function __construct(TableTypeInterface $type) {
        $this->type = $type;
    }
    
    public function getType() {
        return $this->type;
    }

    public function buildQuery(QueryConfigInterface $query, TableInterface $table) {
        $this->type->buildQuery($query, $table);
    }

    public function buildTable(TableBuilderInterface $builder, array $options) {
        $this->type->buildTable($builder, $options);
    }

    public function buildView(TableView $view, TableInterface $table) {
        $this->type->buildView($view, $table);
    }

    public function buildBodyView(array &$view, TableInterface $table) {
        $this->type->buildBodyView($view, $table);
    }

    public function buildFooterView(array &$view, TableInterface $table) {
        $this->type->buildFooterView($view, $table);
    }

    public function buildHeaderView(array &$view, TableInterface $table) {
        $this->type->buildHeaderView($view, $table);
    }
    
    public function buildBodyCellView(array &$view, ColumnInterface $column, array $data) {
        $this->type->buildBodyCellView($view, $column, $data);
    }
    
    public function buildHeaderCellView(array &$view, ColumnInterface $column) {
        $this->type->buildHeaderCellView($view, $column);
    }
    
    public function getName() {
        return $this->type->getName();
    }

    public function getOptionsResolver() {
        return $this->getType()->getOptionsResolver();
    }

    public function getQueryBuilder(ObjectManager $entityManager, array $params) {
        return $this->type->getQueryBuilder($entityManager, $params);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver, array $defaultOptions) {
        return $this->type->setDefaultOptions($resolver, $defaultOptions);
    }
    
    public function resolveParams(array $params, array $data, $preserveKeys = false, $default = array()) {
        return $this->type->resolveParams($params, $data, $preserveKeys, $default);
    }

}

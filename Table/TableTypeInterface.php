<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Provider\QueryConfigInterface;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableTypeInterface {
    
    /**
     * This method build the table object.<br/>
     * It's called in the TableFactoryInterface::create.<br/>
     * <br/>
     * @param \EMC\TableBundle\Table\TableBuilderInterface $builder
     * @param array $options
     */
    public function buildTable(TableBuilderInterface $builder, array $options);
    
    /**
     * This method populate the table view object $view.<br/>
     * It's has to take care of template needs.<br/>
     * It's called in the TableInterface::getView<br/>
     * <br/>
     * @param \EMC\TableBundle\Table\TableView $view
     * @param \EMC\TableBundle\Table\TableInterface $table
     * @param array $options
     */
    public function buildView(TableView $view, TableInterface $table, array $options = array());
    
    /**
     * This method populate the query config $query. @see QueryConfigInterface<br/>
     * <br/>
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $query
     * @param \EMC\TableBundle\Table\TableInterface $table
     * @param array $options
     */
    public function buildQuery(QueryConfigInterface $query, TableInterface $table, array $options = array());
    
    /**
     * Sets the default options for this type.<br/>
     * <br/>
     * Default options are :<br/>
     * 'name'  => string<br/>
     * 'route' => string<br/>
     * 'data'  => null|array<br/>
     * 'params'=> array<br/>
     * 'attrs' => array<br/>
     * 'data_provider' =>  null|EMC\TableBundle\Provider\DataProviderInterface<br/>
     * 'default_sorts' =>  array<br/>
     * 'limit'     => int<br/>
     * 'selector'  => bool<br/>
     * 'caption'   => string<br/>
     * 'route'     => string<br/>
     * 'subtable'  => null|EMC\TableBundle\Table\TableTypeInterface<br/>
     * 'subtable_options'  => array<br/>
     * 'subtable_params'   => array<br/>
     * 
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);
    
    /**
     * This method return the query builder.<br/>
     * This query builder must contains all table aliases declared in the $options['params'] of columns.<br/>
     * In the case of dynamic params (@see TableTypeInterface::setDefaultOptions $options['subtable_params']),<br/>
     * $params is populated while TableBuilderInterface::getTable is called. It contains the params with their values.<br/>
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
     * @param array $params
     * @return \Doctrine\ORM\QueryBuilder The query builder.
     */
    public function getQueryBuilder(ObjectManager $entityManager, array $params);
    
    /**
     * @return string name of the table type (unique)
     */
    public function getName();
}

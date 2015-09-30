<?php

namespace EMC\TableBundle\Table\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Provider\QueryConfigInterface;
use EMC\TableBundle\Table\TableBuilderInterface;
use EMC\TableBundle\Table\TableInterface;
use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\Column\ColumnInterface;

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
     */
    public function buildView(TableView $view, TableInterface $table);

    public function buildHeaderView(array &$view, TableInterface $table);

    public function buildBodyView(array &$view, TableInterface $table);

    public function buildFooterView(array &$view, TableInterface $table);

    /**
     * 
     * @param \EMC\TableBundle\Table\Type\ColumnInterface $column
     * @param array $data
     */
    public function buildBodyCellView(array &$view, ColumnInterface $column, array $data);

    /**
     * 
     * @param \EMC\TableBundle\Table\Column\ColumnInterface $column
     */
    public function buildHeaderCellView(array &$view, ColumnInterface $column);

    /**
     * This method populate the query config $query. @see QueryConfigInterface<br/>
     * <br/>
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $query
     * @param \EMC\TableBundle\Table\TableInterface $table
     */
    public function buildQuery(QueryConfigInterface $query, TableInterface $table);

    /**
     * Sets the default options for this type.<br/>
     * <br/>
     * Default options are :<br/>
     * <ul>
     * <li><b>name</b>  : string <i>Table type name</i></li>
     * <li><b>route</b> : string <i>Point access route default "_table"</i></li>
     * <li><b>data</b>  : null|array <i>Table data. If set, the table is static. Default null</i></li>
     * <li><b>params</b>: array <i>Table parameters @see TableTypeInterface::getQueryBuilder</i></li>
     * <li><b>attrs</b> : array <i>Table Dom element attributes</i></li>
     * <li><b>data_provider</b> : null|EMC\TableBundle\Provider\DataProviderInterface <i> Custom data provider.</i></li>
     * <li><b>default_sorts</b> : array <i>@todo implements default sort columns</i></li>
     * <li><b>limit</b>     : int <i>Max rows per page</i></li>
     * <li><b>caption</b>   : string <i>Table title (caption)</i></li>
     * <li><b>subtable</b>  : null|EMC\TableBundle\Table\Type\TableTypeInterface <i>Subtable type</i></li>
     * <li><b>subtable_options</b>  : array <i>Subtable options</i></li>
     * <li><b>subtable_params</b>   : array <i>Subtable parameters. Same use as "params" option</i></li>
     * <li><b>rows_pad</b>           : bool <i>Fixe table height. Complete table with empty rows until "limit".</i></li>
     * <li><b>rows_params</b>        : array <i>Parameters to inject in the TR Dom element as data</i></li>
     * <li><b>allow_select</b>       : bool <i>Activate selection mode.</i></li>
     * <li><b>export</b>       : array <i>Allowed Export services names. Example : array('pdf', 'excel')</i></li>
     * </ul>
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
     * Returns the configured options resolver used for this type.
     *
     * @return OptionsResolverInterface The options resolver.
     */
    public function getOptionsResolver();
    
    public function resolveParams(array $params, array $data, $preserveKeys = false, $default = array());

    /**
     * @return string name of the table type (unique)
     */
    public function getName();
}

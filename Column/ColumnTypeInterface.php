<?php

namespace EMC\TableBundle\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnTypeInterface {

    /**
     * Adds a column to the given table configuration.
     * 
     * @param ColumnBuilder $builder
     * @param array|null    $data
     * @param array         $options
     */
    public function buildColumn(ColumnBuilderInterface $builder, array $data = null, array $options = array());

    /**
     * Build data column view
     * @param array $view
     * @param \EMC\TableBundle\Column\ColumnInterface $column
     * @param array $options
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options);

    /**
     * Build the header view.
     * @param array $view
     * @param \EMC\TableBundle\Column\ColumnInterface $column
     */
    public function buildHeaderView(array &$view, ColumnInterface $column);

    /**
     * Build the cell view
     * @param array $view
     * @param \EMC\TableBundle\Column\ColumnInterface $column
     * @param array $data
     */
    public function buildCellView(array &$view, ColumnInterface $column, array $data);

    /**
     * Build the footer view.
     * @param array $view
     * @param \EMC\TableBundle\Column\ColumnInterface $column
     * @param array $data
     */
    public function buildFooterView(array &$view, ColumnInterface $column, array $data);

    /**
     * Sets the default options for this type. <i></i></li>
     * <ul>
     * <li><b>name</b>          : string <i>Column type name</i></li>
     * <li><b>title</b>         : string <i>Column title -> TH Dom element content</i></li>
     * <li><b>params</b>        : string|array <i>Columns parameters.</i></li>
     * <li><b>attrs</b>         : array <i>Table Dom element attributes</i></li>
     * <li><b>format</b>        : null|string|callable <i>Value formatter. If string @see sprintf. If function il must return scalar value.</i></li>
     * <li><b>data</b>          : null|array <i>Data column (static)</i></li>
     * <li><b>default</b>       : null|string <i>@todo implement default value</i></li>
     * <li><b>allow_sort</b>    : bool|array <i>Allow sorting on the column. Array of parameters for orderBy or if true, the orderBy is "params". Otherwize (false) sort is disabled</i></li>
     * <li><b>allow_filter</b>  : bool|array <i>Allow filtering on the column. Array of parameters for building the where clause filter or if true, the parameters are "params". Otherwize (false) filtering is disabled</i></li>
     * </ul>
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * Returns column type name.
     * 
     * @return string
     */
    public function getName();
}

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
     * Sets the default options for this type.<br/>
     * <br/>
     * 'name'          => string<br/>
     * 'title'         => string<br/>
     * 'params'        => string|array<br/>
     * 'attrs'         => array<br/>
     * 'format'        => null|string|callable<br/>
     * 'data'          => null|array<br/>
     * 'default'       => null|string<br/>
     * 'allow_sort'    => bool|array<br/>
     * 'allow_filter'  => bool|array<br/>
     * 'is_action'     => bool<br/>
     * 
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

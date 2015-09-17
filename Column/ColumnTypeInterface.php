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
     * @param string      $data
     * @param array       $options
     */
    public function buildColumn(ColumnBuilderInterface $builder, $data = null, array $options = array());
    
    /**
     * Build data column view
     * @param array $view
     * @param \EMC\TableBundle\Column\ColumnInterface $column
     * @param array $options
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options);
    
    public function buildHeaderView(array &$view, ColumnInterface $column);
    public function buildCellView(array &$view, ColumnInterface $column, array $data);
    public function buildFooterView(array &$view, ColumnInterface $column, array $data);

    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * Returns column type name.
     * 
     * @return string
     */
    public function getName();
}

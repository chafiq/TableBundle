<?php

namespace EMC\TableBundle\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ColumnType
 * 
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class ColumnType implements ColumnTypeInterface {

    public function buildColumn(ColumnBuilderInterface $builder, $data = null, array $options = array()) {
        
    }

    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        $type = $column->getType()->getName();
        $view = array(
            'type'          => $type,
            'column_class'  => $type . '_column ' . $options['column_class'],
            'cell_class'    => $options['name'] . '_cell',
            'value'         => self::getValue($options['format'], $data),
            'allow_sort'    => $options['allow_sort'],
            'allow_filter'  => $options['allow_filter'],
            'is_action'     => $options['is_action']
        );
    }
    
    public function buildCellView(array &$view, ColumnInterface $column, array $data) {
        
    }

    public function buildFooterView(array &$view, ColumnInterface $column, array $data) {
        
    }

    public function buildHeaderView(array &$view, ColumnInterface $column) {
        $view = array(
            'sort' => $column->getOption('allow_sort') ? $column->getOption('idx') : 0,
            'title'=> $column->getOption('title')
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $resolver->setDefaults(array(
            'name'      => $this->getName(),
            'title'     => '',
            'params'    => array(),
            'column_class'  => '',
            'data'      => null,
            'default'   => null,
            'format'    => null,
            'allow_sort'    => false,
            'allow_filter'  => false,
            'is_action'     => false
        ));

        $resolver->setAllowedTypes(array(
            'name'          => 'string',
            'title'         => 'string',
            'params'        => array('string', 'array'),
            'column_class'  => 'string',
            'format'        => array('null', 'string', 'callable'),
            'data'          => array('null', 'array'),
            'default'       => array('null', 'string'),
            'allow_sort'    => 'bool',
            'allow_filter'  => 'bool',
            'is_action'     => 'bool'
        ));
        
        $resolver->setNormalizers(array(
            'params' => function(OptionsResolverInterface $resolver, $params) {
                return is_string($params) ? array($params) : $params;
            }
        ));
    }
    
    static protected function getValue($format, array $data) {
        if (is_null($data) || count($data) === 0) {
            return null;
        }
        
        if (is_callable($format)) {
            return call_user_func_array($format, $data);
        }
        
        if (is_string($format)) {
            $args = $data;
            array_unshift($args, $format);
            return call_user_func_array('sprintf', $args);
        }
        
        if ( count($data) === 1 ) {
            return reset($data);
        }
        
        throw new \UnexpectedValueException;
    }

    abstract public function getName();
}

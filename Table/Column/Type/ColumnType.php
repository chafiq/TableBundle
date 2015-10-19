<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Table\Column\ColumnBuilderInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * ColumnType
 * 
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class ColumnType implements ColumnTypeInterface {

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnBuilderInterface $builder, array $data = null, array $options = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        $type = $column->getType()->getName();
        
        if ( !isset($options['attrs']['class']) ) {
            $options['attrs']['class'] = '';
        }
        $options['attrs']['class'] = trim( 'column-' . $type . ' column-' . $options['name'] . ' ' . $options['attrs']['class']);
        
        $view = array(
            'type'          => $type,
            'name'          => $options['name'],
            'attrs'         => $options['attrs'],
            'value'         => static::getValue($options['format'], $data)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildFooterView(array &$view, ColumnInterface $column, array $data) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(array &$view, ColumnInterface $column) {
        $view = array(
            'sort' => $column->getOption('allow_sort'),
            'title'=> $column->getOption('title'),
            'width'=> $column->getOption('width')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $resolver->setDefaults(array(
            'name'      => '',
            'title'     => '',
            'params'    => array(),
            'attrs'     => array(),
            'data'      => null,
            'default'   => null,
            'format'    => null,
            'width'     => null,
            'allow_sort'    => false,
            'allow_filter'  => false
        ));

        $resolver->setAllowedTypes(array(
            'name'          => 'string',
            'title'         => 'string',
            'params'        => array('string', 'array'),
            'attrs'         => 'array',
            'format'        => array('null', 'string', 'int', 'callable'),
            'data'          => array('null', 'array'),
            'default'       => array('null', 'string'),
            'width'         => array('null', 'string'),
            'allow_sort'    => array('bool', 'array'),
            'allow_filter'  => array('bool', 'array')
        ));
        
        $resolver->setNormalizers(array(
            'params' => function(OptionsResolverInterface $resolver, $params) {
                return is_string($params) ? array($params) : $params;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver() {
        return new OptionsResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function isExportable() {
        return true;
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
        
        if (is_int($format)) {
            if ($format >= count($data)) {
                throw new \UnexpectedValueException('Format invalid index : if format is int index, the value must be in 0 ... n-1');
            }
            $data = array_values($data);
            return $data[$format];
        }
        
        if ( count($data) === 1 ) {
            return reset($data);
        }
        
        throw new \UnexpectedValueException;
    }
    
    static protected function toString($name, $value, array $data){
        if (is_callable($value)) {
            $value = call_user_func_array($value, $data);
            if (!is_string($value)) {
                throw new \UnexpectedValueException('option ' . $name . ' callback must return a string');
            }
        }
        return $value;
    }
    
    /**
     * {@inheritdoc}
     */
    abstract public function getName();
}

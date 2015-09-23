<?php

namespace EMC\TableBundle\Table\Column;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnInterface {

    /**
     * This Method return the column type.
     * @return Type\ColumnTypeInterface
     */
    public function getType();
    
    /**
     * This method must return option's value
     * @param string $name
     * @return mixed option's value
     * @throws \InvalidArgumentException
     */
    public function getOption($name);

    /**
     * @return array Column options
     */
    public function getOptions();

    /**
     * This method resolve parameters for an option "$name".<br/>
     * If the option's value is array, we return it.<br/>
     * If it's TRUE, it return $options['params'], null otherwise<br/>
     * @param string $name
     * @return array
     */
    public function resolveAllowedParams($name);
}

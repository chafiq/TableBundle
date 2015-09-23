<?php

namespace EMC\TableBundle\Table\Column;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnRegistryInterface {

    /**
     * Returns a column type by name.<br/>
     * This methods registers the type extensions table the column extensions.<br/>
     * @param string $name The name of the type
     * @return Type\ColumnTypeInterface The type
     * @throws Exception\UnexpectedTypeException  if the passed name is not a string
     * @throws Exception\InvalidArgumentException if the type can not be retrieved column any extension
     */
    public function getType($name);

    /**
     * Returns whether the given column type is supported.
     * @param string $name The name of the type
     * @return bool Whether the type is supported
     */
    public function hasType($name);
}

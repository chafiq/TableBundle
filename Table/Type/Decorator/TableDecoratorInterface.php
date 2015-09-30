<?php

namespace EMC\TableBundle\Table\Type\Decorator;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableDecoratorInterface {
    /**
     * @return \EMC\TableBundle\Table\Type\TableTypeInterface Table type
     */
    public function getType();
}

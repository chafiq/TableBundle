<?php

namespace EMC\TableBundle\Tests\Table\Column\Type;

use EMC\TableBundle\Table\Column\Type\ColumnType;
/**
 * BarType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class BarType extends ColumnType {
    public function getName() {
        return 'bar';
    }
}

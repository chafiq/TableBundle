<?php

namespace EMC\TableBundle\Tests\Table\Type;

use EMC\TableBundle\Table\Type\TableType;

/**
 * TableTypeMock
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableTypeMock extends TableType {
    public function getName() {
        return 'mock';
    }
}

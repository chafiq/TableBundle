<?php

namespace EMC\TableBundle\Tests\Table\Column;

use EMC\TableBundle\Table\Column\ColumnBuilder;
use EMC\TableBundle\Table\Column\Column;

/**
 * ColumnBuilderTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnBuilderTest extends \PHPUnit_Framework_TestCase {
    public function testGetColumn() {
        $columnTypeMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');
        
        $options = array();
        
        $builder = new ColumnBuilder($columnTypeMock, $options);
        
        $this->assertEquals($builder->getColumn(), new Column($columnTypeMock, $options));
    }
}

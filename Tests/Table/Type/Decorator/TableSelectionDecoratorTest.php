<?php

namespace EMC\TableBundle\Tests\Table\Type\Decorator;

use EMC\TableBundle\Provider\QueryResult;
use EMC\TableBundle\Table\Type\Decorator\TableSelectionDecorator;

/**
 * TableSelectionDecoratorTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableSelectionDecoratorTest extends AbstractTableDecoratorTest {

    public function testBuildView() {
        $rows = array(
            array('a' => 1, 'c' => 2),
            array('a' => 3, 'c' => 4)
        );
        $this->tableMock->expects($this->once())
                ->method('getData')
                ->will($this->returnValue(new QueryResult($rows, 5)));

        $this->tableMock->expects($this->exactly(2))
                ->method('getOption')
                ->with('rows_params')
                ->will($this->returnValue(array('a' => 'b', 'c' => 'd')));

        $this->tableTypeMock->expects($this->exactly(2))
                ->method('resolveParams')
                ->willReturnCallback(function($params, $row) {
                    if ($row['a'] === 1) {
                        return array('b' => 1, 'd' => 2);
                    } else {
                        return array('b' => 3, 'd' => 4);
                    }
                });

        $decorator = new TableSelectionDecorator($this->tableTypeMock);

        $view = new \EMC\TableBundle\Table\TableView();
        $decorator->buildView($view, $this->tableMock, array());

        $expected = array(
            'row_1_2' => array( 'b' => 1, 'd' => 2),
            'row_3_4' => array( 'b' => 3, 'd' => 4),
        );
        $this->assertEquals($expected, $view->getData());
    }

    public function testBuildQuery() {
                $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array(
                                'page' => 3,
                                'limit' => 13,
                                'sort'  => -1,
                                'filter' => 'xxx'
                            );
                        case 'rows_params' :
                            return array('a' => 'b', 'c' => 'd');
                        case 'allow_select' :
                            return false;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });

        $queryConfig = clone $this->queryConfig;
        
        $this->tableTypeMock->expects($this->any())
                ->method('buildQuery');

        $decorator = new TableSelectionDecorator($this->tableTypeMock);
        $decorator->buildQuery($queryConfig, $this->tableMock);

        $this->assertEquals(array('b', 'd'), $queryConfig->getSelect());
        $this->assertEquals($this->queryConfig->getConstraints(), $queryConfig->getConstraints());
        $this->assertEquals($this->queryConfig->isValid(), $queryConfig->isValid());
        $this->assertEquals($this->queryConfig->getOrderBy(), $queryConfig->getOrderBy());
        $this->assertEquals($this->queryConfig->getParameters(), $queryConfig->getParameters());
        $this->assertEquals(0, $queryConfig->getLimit());
        $this->assertEquals(1, $queryConfig->getPage());
    }

}

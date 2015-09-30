<?php

namespace EMC\TableBundle\Tests\Table\Type\Decorator;

use EMC\TableBundle\Table\Type\Decorator\TableExportDecorator;

/**
 * TableExportDecoratorTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableExportDecoratorTest extends AbstractTableDecoratorTest {

    public function testBuildHeaderCellViewExportable() {
        $this->columnTypeMock->expects($this->once())
                ->method('isExportable')
                ->will($this->returnValue(true));

        $decorator = new TableExportDecorator($this->tableTypeMock);

        $view = array();
        $decorator->buildHeaderCellView($view, $this->columnMock);

        $this->assertTrue(is_array($view));
        $this->assertEquals($this->expectedView, $view);
    }

    public function testBuildHeaderCellViewNotExportable() {
        $this->columnTypeMock->expects($this->once())
                ->method('isExportable')
                ->will($this->returnValue(false));

        $decorator = new TableExportDecorator($this->tableTypeMock);

        $view = array();
        $decorator->buildHeaderCellView($view, $this->columnMock);

        $this->assertNull($view);
    }

    public function testBuildBodyCellViewExportable() {
        $this->columnTypeMock->expects($this->once())
                ->method('isExportable')
                ->will($this->returnValue(true));

        $decorator = new TableExportDecorator($this->tableTypeMock);

        $view = array();
        $decorator->buildBodyCellView($view, $this->columnMock, array());

        $this->assertTrue(is_array($view));
        $this->assertEquals($this->expectedView, $view);
    }

    public function testBuildBodyCellViewNotExportable() {
        $this->columnTypeMock->expects($this->once())
                ->method('isExportable')
                ->will($this->returnValue(false));

        $decorator = new TableExportDecorator($this->tableTypeMock);

        $view = array();
        $decorator->buildBodyCellView($view, $this->columnMock, array());

        $this->assertNull($view);
    }

    public function testBuildQueryNotAllowedSelect() {
        $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array('selectedRows' => array(
                                    array('a' => 1),
                                    array('a' => 3)
                            ));
                        case 'rows_params' :
                            return array('a' => 'b');
                        case 'allow_select' :
                            return false;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });

        $queryConfig = clone $this->queryConfig;
        
        $this->tableTypeMock->expects($this->any())
                ->method('buildQuery');

        $decorator = new TableExportDecorator($this->tableTypeMock);
        $decorator->buildQuery($queryConfig, $this->tableMock);

        $this->assertEquals($this->queryConfig, $queryConfig);
    }

    public function testBuildQueryAllowedSelectOneRowsParams() {
        $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array('selectedRows' => array(
                                    array('a' => 1),
                                    array('a' => 3)
                            ));
                        case 'rows_params' :
                            return array('a' => 'b');
                        case 'allow_select' :
                            return true;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });

        $decorator = new TableExportDecorator($this->tableTypeMock);
        $decorator->buildQuery($this->queryConfig, $this->tableMock);

        $this->assertEquals(array('b in (:selectedRowIds)'), $this->queryConfig->getConstraints()->getParts());
        $this->assertEquals(1, $this->queryConfig->getConstraints()->count());
    }

    public function testBuildQueryAllowedSelectMultiRowsParams() {
        $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array('selectedRows' => array(
                                    array('a' => 1, 'c' => 2),
                                    array('a' => 3, 'c' => 4)
                            ));
                        case 'rows_params' :
                            return array('a' => 'b', 'c' => 'd');
                        case 'allow_select' :
                            return true;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });
        $this->tableTypeMock->expects($this->exactly(2))
                ->method('resolveParams')
                ->willReturnCallback(function($params, $row) {
                    $this->assertEquals(array('b' => 'a', 'd' => 'c'), $params);
                    if ($row['a'] === 1) {
                        return array('b' => 1, 'd' => 2);
                    } else {
                        return array('b' => 3, 'd' => 4);
                    }
                });


        $decorator = new TableExportDecorator($this->tableTypeMock);
        $decorator->buildQuery($this->queryConfig, $this->tableMock);

        $parts = $this->queryConfig->getConstraints()->getParts();
        $this->assertEquals(2, count($parts));
        $this->assertInstanceOf('Doctrine\ORM\Query\Expr\Andx', $parts[0]);
        $this->assertInstanceOf('Doctrine\ORM\Query\Expr\Andx', $parts[1]);
        $this->assertEquals(array('b = :a0', 'd = :c0'), $parts[0]->getParts());
        $this->assertEquals(array('b = :a1', 'd = :c1'), $parts[1]->getParts());
        $this->assertEquals(2, $parts[0]->count());
        $this->assertEquals(2, $parts[1]->count());
    }

    public function testBuildQueryWithAllowedSelectAnyRowSelected() {
        $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array('selectedRows' => array());
                        case 'rows_params' :
                            return array('a' => 'b', 'c' => 'd');
                        case 'allow_select' :
                            return true;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });

        $queryConfig = clone $this->queryConfig;
        $decorator = new TableExportDecorator($this->tableTypeMock);
        $decorator->buildQuery($queryConfig, $this->tableMock);
        $this->assertFalse($queryConfig->isValid());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildQueryWithAllowedSelectAndNoRowSelectedException() {
        $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array();
                        case 'rows_params' :
                            return array('a' => 'b', 'c' => 'd');
                        case 'allow_select' :
                            return true;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });

        $queryConfig = clone $this->queryConfig;
        $decorator = new TableExportDecorator($this->tableTypeMock);
        $decorator->buildQuery($queryConfig, $this->tableMock);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildQueryWrongMultiRowsParamsException() {
        $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array('selectedRows' => array(
                                    array('a' => 1, 'c' => 2),
                                    array('a' => 3, 'c' => 4)
                            ));
                        case 'rows_params' :
                            return array('a' => 'b', 'c' => 'b');
                        case 'allow_select' :
                            return true;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });

        $queryConfig = clone $this->queryConfig;
        $decorator = new TableExportDecorator($this->tableTypeMock);
        $decorator->buildQuery($queryConfig, $this->tableMock);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildQueryWrongOneRowsParamsException() {
        $this->tableMock->expects($this->any())
                ->method('getOption')
                ->willReturnCallback(function($name) {
                    switch ($name) {
                        case '_query':
                            return array('selectedRows' => array(
                                    array('c' => 1),
                                    array('c' => 3)
                            ));
                        case 'rows_params' :
                            return array('a' => 'b');
                        case 'allow_select' :
                            return true;
                    }
                    throw new \UnexpectedValueException('Table Mock getOption : Unknown ' . $name);
                });

        $queryConfig = clone $this->queryConfig;
        $decorator = new TableExportDecorator($this->tableTypeMock);
        $decorator->buildQuery($queryConfig, $this->tableMock);
    }

}

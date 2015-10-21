<?php

namespace EMC\TableBundle\Tests\Table\Type\Decorator;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Provider\QueryConfig;

/**
 * AbstractTableDecoratorTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class AbstractTableDecoratorTest extends AbstractUnitTest {

    /**
     * @var \EMC\TableBundle\Table\Type\Decorator\TableDecoratorInterface
     */
    protected $decoratorMock;

    /**
     *
     * @var \EMC\TableBundle\Table\TableInterface
     */
    protected $tableMock;

    /**
     * @var \EMC\TableBundle\Table\Type\TableTypeInterface
     */
    protected $tableTypeMock;

    /**
     * @var \EMC\TableBundle\Table\Column\Type\ColumnTypeInterface
     */
    protected $columnTypeMock;

    /**
     * @var \EMC\TableBundle\Table\Column\ColumnInterface
     */
    protected $columnMock;

    /**
     * @var array
     */
    protected $expectedView;

    /**
     * @var \EMC\TableBundle\Provider\QueryConfigInterface
     */
    protected $queryConfig;

    public function setUp() {
        $this->decoratorMock = $this->getMock('EMC\TableBundle\Table\Type\Decorator\TableDecoratorInterface');
        $this->tableTypeMock = $this->getMock('EMC\TableBundle\Table\Type\TableTypeInterface');
        $this->columnTypeMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');
        $this->columnMock = $this->getMock('EMC\TableBundle\Table\Column\ColumnInterface');

        $this->columnMock->expects($this->any())
                ->method('getType')
                ->will($this->returnValue($this->columnTypeMock));

        $expectedView = array('a' => 1, 'b' => '2');
        $this->expectedView = $expectedView;
        $this->tableTypeMock->expects($this->any())
                ->method('buildHeaderCellView')
                ->willReturnCallback(function(&$view, $column) use ($expectedView) {
                    $view = $expectedView;
                });

        $this->tableTypeMock->expects($this->any())
                ->method('buildBodyCellView')
                ->willReturnCallback(function(&$view, $column) use ($expectedView) {
                    $view = $expectedView;
                });

        $this->tableMock = $this->getMock('EMC\TableBundle\Table\TableInterface');
        $this->tableMock
                ->method('getType')
                ->will($this->returnValue($this->tableTypeMock));

        $this->queryConfig = new QueryConfig();
        $this->queryConfig
                ->setLimit(5)
                ->setSelect(array('t.a', 't.b', 't.c'))
                ->setPage(2)
                ->setOrderBy(array('t.d' => true, 't.e' => false))
                ->addParameter('query', '%xxx%')
                ->getConstraints()
                ->add('LOWER(t.a) LIKE :query')
                ->add('LOWER(t.c) LIKE :query');
    }

}

<?php

namespace EMC\TableBundle\Tests\Table;

use EMC\TableBundle\Tests\Table\TableAbstractTest;
use EMC\TableBundle\Table\Table;
use EMC\TableBundle\Table\TableBuilder;
use EMC\TableBundle\Event\TablePreSetDataEvent;
use EMC\TableBundle\Event\TablePostSetDataEvent;

/**
 * TableBuilderTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableBuilderTest extends TableAbstractTest {

    public function testCreate() {
        $this->eventDispatcherMock->expects($this->once())
                ->method('dispatch')
                ->with(TablePreSetDataEvent::NAME);

        $this->assertEquals($this->builder->create(), new Table($this->fooType, array(), $this->resolvedOptions));
    }

    public function testGetTable() {
        $this->eventDispatcherMock->expects($this->at(0))
                ->method('dispatch')
                ->with(TablePreSetDataEvent::NAME);
        $this->eventDispatcherMock->expects($this->at(1))
                ->method('dispatch')
                ->with(TablePostSetDataEvent::NAME);
        $this->builder->getTable();
    }

    public function testAdd() {

        $builder = $this->builder->add('id', 'text');
        $this->assertEquals($builder, $this->builder);
        $this->assertEquals(count($this->builder->getColumns()), 1);

        $this->builder->add('name', 'text');
        $this->assertEquals(count($this->builder->getColumns()), 2);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddException() {
        $this->builder->add('id', 'text');
        $this->builder->add('id', 'text');
    }

    public function testGetQueryConfig() {
        $this->builder->add('id', 'text');
        $this->builder->add('name', 'text');
        $table = $this->builder->create();
        $queryConfig = $this->builder->getQueryConfig($table);

        $expectedQueryConfig = new \EMC\TableBundle\Provider\QueryConfig;
        $this->fooType->buildQuery($expectedQueryConfig, $table, $this->resolvedOptions);

        $this->assertEquals($queryConfig, $expectedQueryConfig);
    }

    public function testGetQueryResultStaticData() {

        $builder = new TableBuilder($this->entityManagerMock, $this->eventDispatcherMock, $this->columnFactoryMock, $this->fooType, $this->defaultColumnOptions, self::$rows, $this->resolvedOptions);

        $builder->add('id', 'text');
        $builder->add('name', 'text');
        $table = $builder->create();
        $queryResult = $builder->getQueryResult($table);

        $expectedQueryResult = new \EMC\TableBundle\Provider\QueryResult(self::$rows, 0);

        $this->assertEquals($queryResult, $expectedQueryResult);
    }

    public function testGetQueryResult() {
        $this->builder->add('id', 'text');
        $this->builder->add('name', 'text');
        $table = $this->builder->create();
        $queryResult = $this->builder->getQueryResult($table);
        
        $this->assertEquals($queryResult->getRows(), self::$rows);
        $this->assertEquals($queryResult->getCount(), count(self::$rows));
    }

}

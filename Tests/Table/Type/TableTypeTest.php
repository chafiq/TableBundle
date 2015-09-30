<?php

namespace EMC\TableBundle\Tests\Table\Type;

use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Tests\Table\TableAbstractTest;
use EMC\TableBundle\Provider\QueryConfig;
use EMC\TableBundle\Table\TableBuilder;

/**
 * TableTypeTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableTypeTest extends TableAbstractTest {

    public function testBuildView() {
        $view = new TableView();

        $this->builder->add('id', 'text');
        $this->builder->add('name', 'text');
        $table = $this->builder->getTable();
        $table->getType()->buildView($view, $table);

        $expectedViewKeys = array(
            'id',
            'subtid',
            'params',
            'attrs',
            'caption',
            'thead',
            'tbody',
            'tfoot',
            'total',
            'limit',
            'page',
            'has_filter',
            'route',
            'rows_pad',
            'allow_select'
        );

        $data = $view->getData();
        foreach ($expectedViewKeys as $key) {
            $this->assertArrayHasKey($key, $data);
        }
        $this->assertEquals(count($table->getColumns()), count($data['thead']));
        $this->assertEquals(count(self::$rows), count($data['tbody']));
    }

    public function testBuildBodyViewEmptyData() {
        $builder = new TableBuilder($this->entityManagerMock, $this->eventDispatcherMock, $this->columnFactoryMock, $this->fooType, array(), $this->resolvedOptions);
        $table = $builder->getTable();
        $view = array();
        $this->assertEquals(array(), $table->getType()->buildBodyView($view, $table));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildViewException() {
        $view = new TableView();
        $table = $this->builder->getTable();
        $options = $table->getOptions();
        unset($options['_tid']);
        
        
        $this->invokeSetter($table, 'options', $options);
        
        $table->getType()->buildView($view, $table);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildViewUnknownParamsException() {
        $view = new TableView();
        $this->builder->add('test', 'text');
        $table = $this->builder->getTable();
        $table->getType()->buildView($view, $table);
    }

    public function testBuildQuery() {

        $this->builder->add('id', 'text');
        $this->builder->add('name', 'text');
        $this->builder->add('test', 'text');

        $table = $this->builder->create();

        $this->resolvedOptions['_query'] = array(
            'limit' => 15,
            'page' => 3,
            'filter' => 'test',
            'sort' => -2
        );

        $this->resolvedOptions['rows_params'] = array('f' => 'g', 'h');
        $this->resolvedOptions['subtable_params'] = array('h' => 'k', 'l' => 'm');

        $this->invokeSetter($table, 'options', $this->resolvedOptions);
        
        $queryConfig = new QueryConfig();
        $table->getType()->buildQuery($queryConfig, $table);
        
        $this->assertEquals(array('k', 'm', 'g', 'h', 'id', 'name', 'x'), $queryConfig->getSelect());
        $this->assertEquals(array('LOWER(name) LIKE :query', 'LOWER(z) LIKE :query'), $queryConfig->getConstraints()->getParts());
        $this->assertEquals($this->resolvedOptions['_query']['limit'], $queryConfig->getLimit());
        $this->assertEquals($this->resolvedOptions['_query']['page'], $queryConfig->getPage());
        $this->assertEquals(array('name' => false, 'id' => false), $queryConfig->getOrderBy());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildQueryFilterException() {

        $this->builder->add('name', 'text');
        $table = $this->builder->create();

        $this->resolvedOptions['_query']['filter'] = 'xx';

        $this->invokeSetter($table, 'options', $this->resolvedOptions);
        
        $queryConfig = new QueryConfig();
        $table->getType()->buildQuery($queryConfig, $table);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testBuildQuerySortException() {

        $this->builder->add('name', 'text');
        $table = $this->builder->create();

        $this->resolvedOptions['_query']['sort'] = -5;

        $this->invokeSetter($table, 'options', $this->resolvedOptions);
        
        $queryConfig = new QueryConfig();
        $table->getType()->buildQuery($queryConfig, $table);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testBuildQuerySortNotAllowedException() {

        $this->builder->add('id', 'text');
        $table = $this->builder->create();

        $this->resolvedOptions['_query']['sort'] = 1;

        $this->invokeSetter($table, 'options', $this->resolvedOptions);
        
        $queryConfig = new QueryConfig();
        $table->getType()->buildQuery($queryConfig, $table);
    }

    public function testHasFilter() {
        $tests = array(
            array(
                'expected' => true,
                'columns' => array('name')
            ),
            array(
                'expected' => false,
                'columns' => array('id')
            ),
            array(
                'expected' => true,
                'columns' => array('test')
            )
        );

        foreach ($tests as $test) {
            $builderClone = clone $this->builder;
            foreach ($test['columns'] as $column) {
                $builderClone->add($column, 'text');
            }
            $this->assertEquals($test['expected'], $this->invokeMethod($this->fooType, 'hasFilter', array($builderClone->getTable())));
        }
    }

    public function testResolveParams() {
        $tests = array(
            array(
                'expected' => array('a' => 1, 'b' => 2),
                'params' => array('a', 'b')
            ),
            array(
                'expected' => array('c' => 1, 'b' => 2),
                'params' => array('c' => 'a', 'b')
            )
        );

        foreach ($tests as $test) {
            $this->assertEquals($test['expected'], $this->invokeMethod($this->fooType, 'resolveParams', array($test['params'], array('a' => 1, 'b' => 2, 'x' => 3))));
        }
        
        $this->assertEquals(array('a' => null, 'b' => 2), $this->invokeMethod($this->fooType, 'resolveParams', array(array('a', 'b'), array('a' => null, 'b' => 2, 'x' => 3))));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testResolveParamsException() {
        $this->invokeMethod($this->fooType, 'resolveParams', array(array('a', 'c' => 'b', 'd'), array('a' => 1, 'b' => 2, 'x' => 3)));
    }

}

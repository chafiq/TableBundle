<?php

namespace EMC\TableBundle\Tests\Provider;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Provider\QueryConfig;
use EMC\TableBundle\Provider\DataProvider;

/**
 * DataProvider
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class DataProviderTest extends AbstractUnitTest {

    /**
     * @var \EMC\TableBundle\Provider\DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var \EMC\TableBundle\Provider\QueryConfigInterface
     */
    private $queryConfig;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;

    public function setUp() {
        $this->queryConfig = new QueryConfig();
        $this->queryConfig
                ->setLimit(5)
                ->setSelect(array('t.a', 't.b', 't.c'))
                ->setPage(2)
                ->setQuery('xxx')
                ->setOrderBy(array('t.d' => true, 't.e' => false))
                ->setFilters(array('t.a', 't.c'));

        $this->queryBuilder = new QueryBuilderMock();
        $this->queryBuilder->from('Table', 't');

        $this->dataProvider = new DataProvider();
    }

    public function testFind() {
        $result = $this->dataProvider->find($this->queryBuilder, $this->queryConfig);
        $this->assertInstanceOf('EMC\TableBundle\Provider\QueryResultInterface', $result);
    }

    public function testFindWithoutLimit() {
        $queryConfig = clone $this->queryConfig;
        $queryConfig->setLimit(0);
        $result = $this->dataProvider->find($this->queryBuilder, $queryConfig);
        $this->assertInstanceOf('EMC\TableBundle\Provider\QueryResultInterface', $result);
        $this->assertEquals(0, $result->getCount());
    }

    
    public function testFindAll() {
        $queryConfig = clone $this->queryConfig;
        $queryConfig->setLimit(0);
        $queryConfig->setPage(1);
        $queryBuilder = clone $this->queryBuilder;
        $result = $this->dataProvider->find($queryBuilder, $queryConfig);
        $this->assertEquals($result, $this->dataProvider->findAll($this->queryBuilder, $this->queryConfig));
    }

    public function testAddConstraints() {
        $this->queryBuilder->select('t.a');
        $this->invokeMethod($this->dataProvider, 'addConstraints', array($this->queryBuilder, $this->queryConfig));
        $query = $this->queryBuilder->getQuery();
        $this->assertEquals('SELECT t.a FROM Table t WHERE LOWER(t.a) LIKE :query OR LOWER(t.c) LIKE :query', $query->getDQL());
        $this->assertEquals(1, $query->getParameters()->count());

        /* @var $paramater \Doctrine\ORM\Query\Parameter */
        $parameter = $query->getParameters()->get(0);
        $this->assertEquals('query', $parameter->getName());
        $this->assertEquals(2, $parameter->getType());
        $this->assertEquals('%xxx%', $parameter->getValue());
    }

    public function testGetQueryRows() {
        $columns = array();
        $this->invokeMethod($this->dataProvider, 'addConstraints', array($this->queryBuilder, $this->queryConfig));

        $query = $this->invokeMethod($this->dataProvider, 'getQueryRows', array($this->queryBuilder, $this->queryConfig, &$columns));

        $this->assertEquals(    'SELECT t.a AS col0, t.b AS col1, t.c AS col2 FROM Table t '
                            .   'WHERE LOWER(t.a) LIKE :query OR LOWER(t.c) LIKE :query '
                            .   'ORDER BY t.e DESC', $query->getDQL());

        $this->assertEquals(1, $query->getParameters()->count());

        /* @var $paramater \Doctrine\ORM\Query\Parameter */
        $parameter = $query->getParameters()->get(0);
        $this->assertEquals('query', $parameter->getName());
        $this->assertEquals(2, $parameter->getType());
        $this->assertEquals('%xxx%', $parameter->getValue());

        $this->assertEquals(array('t.a' => 'col0', 't.b' => 'col1', 't.c' => 'col2'), $columns);
    }
    
    public function testGetQueryRowsNoOrderBy() {
        $columns = array();
        $queryConfig = clone $this->queryConfig;
        $queryConfig->setOrderBy(array());
        $this->invokeMethod($this->dataProvider, 'addConstraints', array($this->queryBuilder, $queryConfig));

        $query = $this->invokeMethod($this->dataProvider, 'getQueryRows', array($this->queryBuilder, $queryConfig, &$columns));

        $this->assertEquals('SELECT t.a AS col0, t.b AS col1, t.c AS col2 FROM Table t WHERE LOWER(t.a) LIKE :query OR LOWER(t.c) LIKE :query ORDER BY t.id ASC', $query->getDQL());
        $this->assertEquals(1, $query->getParameters()->count());

        /* @var $paramater \Doctrine\ORM\Query\Parameter */
        $parameter = $query->getParameters()->get(0);
        $this->assertEquals('query', $parameter->getName());
        $this->assertEquals(2, $parameter->getType());
        $this->assertEquals('%xxx%', $parameter->getValue());

        $this->assertEquals(array('t.a' => 'col0', 't.b' => 'col1', 't.c' => 'col2'), $columns);
    }

    public function testGetQueryCount() {
        $query = $this->invokeMethod($this->dataProvider, 'getQueryCount', array($this->queryBuilder, $this->queryConfig));
        $this->assertEquals('SELECT count(distinct t.id) FROM Table t', $query->getDQL());
        $this->assertEquals(0, $query->getParameters()->count());
    }

    public function testResolveRowsKeys() {
        $rows = array(
            array('col0' => 1, 'col1' => 2),
            array('col0' => 3, 'col1' => 4)
        );
        $expected = array(
            array('x' => 1, 'y' => 2),
            array('x' => 3, 'y' => 4)
        );
        
        $result = $this->invokeMethod($this->dataProvider, 'resolveRowsKeys', array($rows, array('x' => 'col0', 'y' => 'col1')));
        $this->assertEquals($expected, $result);
    }

    
    public function testNoConstraints() {
        $queryBuilderClone = clone $this->queryBuilder;
        
        $queryConfig = clone $this->queryConfig;
        $queryConfig->setFilters(array());
        $queryBuilder = $this->invokeMethod($this->dataProvider, 'addConstraints', array($queryBuilderClone, $queryConfig));
        $this->assertEquals($queryBuilderClone, $queryBuilder);
        
        $queryConfig = clone $this->queryConfig;
        $queryConfig->setQuery(null);
        $queryBuilder = $this->invokeMethod($this->dataProvider, 'addConstraints', array($queryBuilderClone, $queryConfig));
        $this->assertEquals($queryBuilderClone, $queryBuilder);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstraintsException() {
        $queryConfig = clone $this->queryConfig;
        $queryBuilderClone = clone $this->queryBuilder;
        $queryConfig->setQuery('x');
        $this->invokeMethod($this->dataProvider, 'addConstraints', array($queryBuilderClone, $queryConfig));
    }
}

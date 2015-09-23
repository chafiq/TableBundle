<?php

namespace EMC\TableBundle\Tests\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Table\Table;
use EMC\TableBundle\Table\TableBuilder;
use EMC\TableBundle\Event\TablePreSetDataEvent;
use EMC\TableBundle\Event\TablePostSetDataEvent;
use EMC\TableBundle\Provider\QueryResult;
use EMC\TableBundle\Table\Column\ColumnBuilder;

/**
 * TableBuilderTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableBuilderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManagerMock;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcherMock;

    /**
     * @var \EMC\TableBundle\Table\Column\ColumnFactoryInterface
     */
    private $columnFactoryMock;

    /**
     * @var \EMC\TableBundle\Table\TableBuilder
     */
    private $builder;

    /**
     * @var \EMC\TableBundle\Provider\DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var \EMC\TableBundle\Table\Type\TableTypeInterface
     */
    private $fooType;

    /**
     * @var array
     */
    private $resolvedOptions;

    /**
     * @var \EMC\TableBundle\Provider\QueryResultInterface
     */
    private $queryResult;

    protected function setUp() {
        $this->entityManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->columnFactoryMock = $this->getMock('EMC\TableBundle\Table\Column\ColumnFactoryInterface');

        $columnTypeIdMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');
        $columnTypeNameMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');

        $columnBuilderId = new ColumnBuilder($columnTypeIdMock, $this->getResolvedOptions(array(
                    'params' => array('a'),
                    'allow_sort' => true
        )));
        $columnBuilderName = new ColumnBuilder($columnTypeNameMock, $this->getResolvedOptions(array(
                    'params' => array('b', 'c'),
                    'allow_sort' => array('b', 'd'),
                    'allow_filter' => true
        )));

        $this->columnFactoryMock->expects($this->any())
                ->method('create')
                ->with($this->logicalOr(
                                $this->equalTo('id'), $this->equalTo('name')
                ))
                ->will($this->returnCallback(function($name) use ($columnBuilderId, $columnBuilderName) {
                            return $name === 'id' ? $columnBuilderId : $columnBuilderName;
                        }));

        $this->queryResult = new QueryResult(self::$rows, count(self::$rows));

        $this->dataProvider = $this->getMock('EMC\TableBundle\Provider\DataProviderInterface');

        $this->dataProvider->expects($this->any())
                ->method('find')
                ->will($this->returnValue($this->queryResult));

        $options = array('data_provider' => $this->dataProvider);

        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $this->fooType = new Type\FooType($queryBuilder);

        $optionsResolver = new OptionsResolver();
        $this->fooType->setDefaultOptions($optionsResolver);

        $this->resolvedOptions = $optionsResolver->resolve($options);
        $this->resolvedOptions['_query'] = array('page' => 1, 'sort' => 0, 'limit' => 10, 'filter' => null);

        $this->builder = new TableBuilder($this->entityManagerMock, $this->eventDispatcherMock, $this->columnFactoryMock, $this->fooType, null, $this->resolvedOptions);
    }

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

        $builder = new TableBuilder($this->entityManagerMock, $this->eventDispatcherMock, $this->columnFactoryMock, $this->fooType, self::$rows, $this->resolvedOptions);

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

    public function getResolvedOptions(array $options) {
        $expectedResolvedOptions = array(
            'name',
            'title',
            'params',
            'attrs',
            'data',
            'default',
            'format',
            'allow_sort',
            'allow_filter'
        );

        $textType = new \EMC\TableBundle\Table\Column\Type\TextType;
        $resolver = new OptionsResolver();
        $textType->setDefaultOptions($resolver);

        $resolvedOptions = $resolver->resolve($options);

        foreach ($expectedResolvedOptions as $option) {
            $this->assertArrayHasKey($option, $resolvedOptions);
        }

        return $resolvedOptions;
    }

    private static $rows = array(
        array('id' => 1, 'name' => 'Aquitaine'),
        array('id' => 2, 'name' => 'Auvergne'),
        array('id' => 3, 'name' => 'Bourgogne'),
        array('id' => 4, 'name' => 'Bretagne'),
        array('id' => 5, 'name' => 'Centre'),
        array('id' => 6, 'name' => 'Champagne Ardenne'),
        array('id' => 7, 'name' => 'Corse'),
        array('id' => 8, 'name' => 'DOM/TOM'),
        array('id' => 9, 'name' => 'Franche Comté'),
        array('id' => 10, 'name' => 'Ile de France'),
        array('id' => 11, 'name' => 'Languedoc Roussillon'),
        array('id' => 12, 'name' => 'Limousin'),
        array('id' => 13, 'name' => 'Lorraine'),
        array('id' => 14, 'name' => 'Midi Pyrénées'),
        array('id' => 15, 'name' => 'Nord Pas de Calais'),
        array('id' => 17, 'name' => 'Haute Normandie '),
        array('id' => 18, 'name' => 'Pays de la Loire'),
        array('id' => 19, 'name' => 'Picardie'),
        array('id' => 20, 'name' => 'Poitou Charentes'),
        array('id' => 21, 'name' => 'Provence Alpes Côte d\'azur'),
        array('id' => 22, 'name' => 'Rhône Alpes'),
        array('id' => 23, 'name' => 'Alsace'),
        array('id' => 24, 'name' => 'Basse-Normandie'),
    );

}

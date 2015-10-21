<?php

namespace EMC\TableBundle\Tests\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Table\TableBuilder;
use EMC\TableBundle\Provider\QueryResult;
use EMC\TableBundle\Table\Column\ColumnBuilder;

/**
 * TableBuilderTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class TableAbstractTest extends AbstractUnitTest {

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManagerMock;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcherMock;

    /**
     * @var \EMC\TableBundle\Table\Column\ColumnFactoryInterface
     */
    protected $columnFactoryMock;

    /**
     * @var \EMC\TableBundle\Table\TableBuilder
     */

    /**
     * @var \EMC\TableBundle\Provider\DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var \EMC\TableBundle\Table\Type\TableTypeInterface
     */
    protected $fooType;

    /**
     * @var array
     */
    protected $resolvedOptions;

    /**
     * @var \EMC\TableBundle\Provider\QueryResultInterface
     */
    protected $queryResult;
    
    /**
     * @var \EMC\TableBundle\Table\TableBuilderInterface
     */
    protected $builder;

    public function setUp() {
        $this->entityManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->columnFactoryMock = $this->getMock('EMC\TableBundle\Table\Column\ColumnFactoryInterface');
        
        $columnTypeIdMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');
        $columnTypeNameMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');
        $columnTypeTestMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');

        $that = $this;
        $this->columnFactoryMock->expects($this->any())
                ->method('create')
                ->with($this->logicalOr(
                                $this->equalTo('id'), $this->equalTo('name'), $this->equalTo('test')
                ))
                ->will($this->returnCallback(function($name, $type, $options) use ($that, $columnTypeIdMock, $columnTypeNameMock, $columnTypeTestMock) {
                            $options['name'] = $name;
                            switch ($name) {
                                case 'id' : 
                                    return new ColumnBuilder($columnTypeIdMock, $that->getResolvedOptions(array_merge($options, array(
                                        'params' => array('id'),
                                        'allow_sort' => false
                                    ))));
                                case 'name' :
                                    return new ColumnBuilder($columnTypeNameMock, $that->getResolvedOptions(array_merge($options, array(
                                        'params' => array('name'),
                                        'allow_sort' => array('name', 'id'),
                                        'allow_filter' => true
                                    ))));
                                default :
                                    return new ColumnBuilder($columnTypeTestMock, $that->getResolvedOptions(array_merge($options, array(
                                        'params' => array( 'w' => 'x'),
                                        'allow_sort' => array('y'),
                                        'allow_filter' => array('z')
                                    ))));
                            }
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
        $this->fooType->setDefaultOptions($optionsResolver, $this->defaultOptions);

        $this->resolvedOptions = $optionsResolver->resolve($options);
        $this->resolvedOptions['_tid'] = 'test';
        $this->resolvedOptions['_passed_options'] = array();
        $this->resolvedOptions['_query'] = array('page' => 1, 'sort' => 0, 'limit' => 10, 'filter' => null);

        $this->builder = new TableBuilder($this->entityManagerMock, $this->eventDispatcherMock, $this->columnFactoryMock, $this->fooType, $this->defaultColumnOptions, null, $this->resolvedOptions);
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
        $textType->setDefaultOptions($resolver, $this->defaultColumnOptions);

        $resolvedOptions = $resolver->resolve($options);

        foreach ($expectedResolvedOptions as $option) {
            $this->assertArrayHasKey($option, $resolvedOptions);
        }

        $resolvedOptions['_passed_options'] = $options;
        
        return $resolvedOptions;
    }
    
    public static $rows = array(
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

<?php

namespace EMC\TableBundle\Tests\Table\Type\Decorator;

use EMC\TableBundle\Tests\Table\Type\FooType;
use EMC\TableBundle\Table\TableBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Table\TableView;

/**
 * TableDecoratorTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableDecoratorTest extends AbstractTableDecoratorTest {

    /**
     * @var \EMC\TableBundle\Table\Type\Decorator\TableDecorator;
     */
    private $decorator;

    /**
     *
     * @var FooType
     */
    private $fooType;

    public function setUp() {
        parent::setUp();

        $this->tableMock->expects($this->any())
                ->method('getColumns')
                ->will($this->returnValue(array()));
        
        $queryBuilderMock = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
                                ->disableOriginalConstructor()
                                    ->getMock();
        
        $this->fooType = new FooType($queryBuilderMock);
        $resolver = new OptionsResolver();
        $this->fooType->setDefaultOptions($resolver);
        $resolvedOptions = $resolver->resolve(array());
        $resolvedOptions['_tid'] = 'xxx';
        $resolvedOptions['_query'] = array('page' => 1, 'limit' => 11, 'sort' => 0, 'filter' => 'abc');
        
        $this->tableMock->expects($this->any())
                ->method('getOptions')
                ->will($this->returnValue($resolvedOptions));
        
        $this->columnMock->expects($this->any())
                ->method('getOption')
                ->with('params')
                ->will($this->returnValue(array('a' => 'b', 'c' => 'd')));
        
        $this->columnMock->expects($this->any())
                ->method('getOptions')
                ->will($this->returnValue(array('params' => array('a' => 'b', 'c' => 'd'))));
        
        $this->decorator = new TableDecoratorMock($this->fooType);
    }

    public function testGetType() {
        $this->assertEquals($this->fooType, $this->decorator->getType());
    }

    public function testBuildQuery() {
        $queryConfig = clone $this->queryConfig;
        $expectedQueryConfig = clone $this->queryConfig;
        $this->decorator->buildQuery($queryConfig, $this->tableMock);
        $this->fooType->buildQuery($expectedQueryConfig, $this->tableMock);
        $this->assertEquals($expectedQueryConfig, $queryConfig);
    }

    public function testBuildTable() {
        $entityManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $columnFactoryMock = $this->getMock('EMC\TableBundle\Table\Column\ColumnFactoryInterface');

        $builder = new TableBuilder($entityManagerMock, $eventDispatcherMock, $columnFactoryMock, $this->fooType, null, array());
        
        $options = array('a' => 1, 'b' => '2');
        
        $expectedBuilder = clone $builder;
        $this->decorator->buildTable($builder, $options);
        $this->fooType->buildTable($expectedBuilder, $options);
        $this->assertEquals($expectedBuilder, $builder);
    }

    public function testBuildView() {
        $view = new TableView();
        $expectedView = clone $view;
        $this->decorator->buildView($view, $this->tableMock);
        $this->fooType->buildView($expectedView, $this->tableMock);
        $this->assertEquals($expectedView, $view);
    }

    public function testBuildBodyView() {
        $view = array('_tid' => 'xxx', 'a' => 1, 'b' => '2');
        $expectedView = $view;
        $this->decorator->buildBodyView($view, $this->tableMock);
        $this->fooType->buildBodyView($expectedView, $this->tableMock);
        $this->assertEquals($expectedView, $view);
    }

    public function testBuildFooterView() {
        $view = array('_tid' => 'xxx', 'a' => 1, 'b' => '2');
        $expectedView = $view;
        $this->decorator->buildFooterView($view, $this->tableMock);
        $this->fooType->buildFooterView($expectedView, $this->tableMock);
        $this->assertEquals($expectedView, $view);
    }

    public function testBuildHeaderView() {
        $view = array('_tid' => 'xxx', 'a' => 1, 'b' => '2');
        $expectedView = $view;
        $this->decorator->buildHeaderView($view, $this->tableMock);
        $this->fooType->buildHeaderView($expectedView, $this->tableMock);
        $this->assertEquals($expectedView, $view);
    }

    public function testBuildBodyCellView() {
        $view = array('_tid' => 'xxx', 'a' => 1, 'b' => '2');
        $expectedView = $view;
        $this->decorator->buildBodyCellView($view, $this->columnMock, array('b' => 1, 'd' => 2));
        $this->fooType->buildBodyCellView($expectedView, $this->columnMock, array('b' => 1, 'd' => 2));
        $this->assertEquals($expectedView, $view);
    }

    public function testBuildHeaderCellView() {
        $view = array('_tid' => 'xxx', 'a' => 1, 'b' => '2');
        $expectedView = $view;
        $this->decorator->buildHeaderCellView($view, $this->columnMock);
        $this->fooType->buildHeaderCellView($expectedView, $this->columnMock);
        $this->assertEquals($expectedView, $view);
    }

    public function testGetName() {
        $name = $this->decorator->getName();
        $expectedName = $this->fooType->getName();
        $this->assertEquals($expectedName, $name);
    }

    public function testGetOptionsResolver() {
        $optionsResolver = $this->decorator->getOptionsResolver();
        $expectedOptionsResolver = $this->fooType->getOptionsResolver();
        $this->assertEquals($expectedOptionsResolver, $optionsResolver);
    }

    public function testGetQueryBuilder() {
        
        $entityManagerMock = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
                                ->disableOriginalConstructor()
                                    ->getMock();
        
        $queryBuilder = $this->decorator->getQueryBuilder($entityManagerMock, array());
        $expectedQueryBuilder = $this->fooType->getQueryBuilder($entityManagerMock, array());
        $this->assertEquals($expectedQueryBuilder, $queryBuilder);
    }

    public function testSetDefaultOptions() {
        
        $options = array();
        $resolver = new OptionsResolver();
        $this->decorator->setDefaultOptions($resolver);
        $resolvedOptions = $resolver->resolve($options);
        
        $this->fooType->setDefaultOptions($resolver);
        $expectedResolvedOptions = $resolver->resolve($options);
        $this->assertEquals($expectedResolvedOptions, $resolvedOptions);
    }

    public function testResolveParams() {
        $params = array('a' => 'b', 'c' => 'd');
        $data = array('b' => 2, 'd' => 3);
        
        $resolvedParams = $this->decorator->resolveParams($params, $data);
        $expectedResolvedParams = $this->fooType->resolveParams($params, $data);
        $this->assertEquals($expectedResolvedParams, $resolvedParams);
        
        $resolvedParams = $this->decorator->resolveParams($params, $data, true);
        $expectedResolvedParams = $this->fooType->resolveParams($params, $data, true);
        $this->assertEquals($expectedResolvedParams, $resolvedParams);
    }

}

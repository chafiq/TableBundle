<?php

namespace EMC\TableBundle\Tests\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Table\TableFactory;
use EMC\TableBundle\Tests\AbstractUnitTest;

class TableFactoryTest extends AbstractUnitTest {

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManagerMock;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcherMock;

    /**
     * @var \EMC\TableBundle\Session\TableSessionInterface
     */
    private $tableSessionMock;

    /**
     * @var \EMC\TableBundle\Table\Column\ColumnFactoryInterface
     */
    private $columnFactoryMock;

    /**
     * @var \EMC\TableBundle\Table\Export\ExportRegistryInterface
     */
    private $exportRegistryMock;

    /**
     * @var \EMC\TableBundle\Table\TableFactoryInterface
     */
    private $factory;

    /**
     * @var \EMC\TableBundle\Table\Type\TableTypeInterface
     */
    private $fooType;

    protected function setUp() {
        $this->entityManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->tableSessionMock = $this->getMock('EMC\TableBundle\Session\TableSessionInterface');
        $this->columnFactoryMock = $this->getMock('EMC\TableBundle\Table\Column\ColumnFactoryInterface');
        $this->exportRegistryMock = $this->getMock('EMC\TableBundle\Table\Export\ExportRegistryInterface');

        $this->factory = new TableFactory($this->entityManagerMock, $this->eventDispatcherMock, $this->tableSessionMock, $this->columnFactoryMock, $this->exportRegistryMock, $this->defaultOptions, $this->defaultColumnOptions);

        $this->fooType = new Type\FooType;
        $this->tableSessionMock->expects($this->any())
                ->method('restore')
                ->with($this->fooType->getId())
                ->will($this->returnValue(array(
                            'class' => get_class($this->fooType),
                            'options' => array(),
                            'data' => null
        )));
    }

    public function testCreate() {
        $typeMock = $this->getMock('EMC\TableBundle\Table\Type\TableTypeInterface');

        $options = array();
        $optionsResolverMock = $this->getOptionsResolverMock($options);

        $typeMock->expects($this->once())
                ->method('getName')
                ->will($this->returnValue('foo'));

        $typeMock->expects($this->once())
                ->method('getOptionsResolver')
                ->will($this->returnValue($optionsResolverMock));

        $typeMock->expects($this->once())
                ->method('setDefaultOptions')
                ->with($optionsResolverMock);

        $typeMock->expects($this->once())
                ->method('buildTable');

        $builder = $this->factory->create($typeMock, null, $options);

        $resolvedOptions = $builder->getOptions();
        $this->assertArrayHasKey('_tid', $resolvedOptions);
        $this->assertArrayHasKey('_query', $resolvedOptions);
        $this->assertArrayHasKey('_passed_options', $resolvedOptions);

        $this->assertEquals(strlen($resolvedOptions['_tid']), 40);

        $this->assertArrayHasKey('page', $resolvedOptions['_query']);
        $this->assertArrayHasKey('limit', $resolvedOptions['_query']);
        $this->assertArrayHasKey('filter', $resolvedOptions['_query']);
        $this->assertArrayHasKey('sort', $resolvedOptions['_query']);
    }

    public function testCreateWithSubtable() {

        $subtableOptions = array();
        $subtypeMock = $this->getTypeMock($subtableOptions);

        $options = array(
            'subtable' => $subtypeMock,
            'subtable_params' => array('a' => 'b', 'b' => 'c'),
            'subtable_options' => $subtableOptions
        );
        $typeMock = $this->getTypeMock($options);

        $builder = $this->factory->create($typeMock, null, $options);

        $resolvedOptions = $builder->getOptions();

        $this->assertArrayHasKey('_subtid', $resolvedOptions);
        $this->assertEquals(strlen($resolvedOptions['_subtid']), 40);
    }

    public function testLoad() {
        $class = 'EMC\TableBundle\Tests\Table\Type\FooType';
        $builder = $this->factory->load($class);
        $this->assertEquals($this->factory->create(new Type\FooType()), $builder);
    }

    public function testRestore() {
        $builder = $this->factory->restore($this->fooType->getId());
        $expected = $this->factory->create(new Type\FooType());
        $this->assertEquals($expected, $builder);
    }

    public function testGetResolvedType() {
        $type = $this->invokeMethod($this->factory, 'getResolvedType', array($this->fooType, TableFactory::MODE_NORMAL));
        $this->assertEquals($this->fooType, $type);
        $type = $this->invokeMethod($this->factory, 'getResolvedType', array($this->fooType, TableFactory::MODE_EXPORT));
        $this->assertInstanceOf('EMC\TableBundle\Table\Type\Decorator\TableExportDecorator', $type);
        $type = $this->invokeMethod($this->factory, 'getResolvedType', array($this->fooType, TableFactory::MODE_SELECTION));
        $this->assertInstanceOf('EMC\TableBundle\Table\Type\Decorator\TableSelectionDecorator', $type);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetResolvedTypeInvalidArgumentException() {
        $this->invokeMethod($this->factory, 'getResolvedType', array($this->fooType, 'a'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetResolvedTypeUnexpectedValueException() {
        $this->invokeMethod($this->factory, 'getResolvedType', array($this->fooType, 123));
    }

    public function testNewInstance() {
        $class = 'EMC\TableBundle\Tests\Table\Type\FooType';
        $type = $this->invokeMethod($this->factory, 'newInstance', array($class));
        $this->assertInstanceOf($class, $type);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNewInstanceInvalidArgumentException() {
        $this->invokeMethod($this->factory, 'newInstance', array('\stdClass'));
    }

    public function getTypeMock(array $options) {

        $optionsResolverMock = $this->getOptionsResolverMock($options);

        $typeMock = $this->getMock('EMC\TableBundle\Table\Type\TableTypeInterface');

        $typeMock->expects($this->once())
                ->method('getName')
                ->will($this->returnValue('foo'));

        $typeMock->expects($this->once())
                ->method('getOptionsResolver')
                ->will($this->returnValue($optionsResolverMock));

        $typeMock->expects($this->once())
                ->method('setDefaultOptions')
                ->with($optionsResolverMock);

        $typeMock->expects($this->once())
                ->method('buildTable');

        return $typeMock;
    }

    public function getOptionsResolverMock(array $options) {
        $expectedResolvedOptions = array(
            'name',
            'route',
            'data',
            'params',
            'attrs',
            'data_provider',
            'default_sorts',
            'limit',
            'caption',
            'subtable',
            'subtable_options',
            'subtable_params',
            'rows_pad',
            'rows_params',
            'allow_select'
        );

        $optionsResolver = new OptionsResolver();
        $type = new Type\FooType();
        $type->setDefaultOptions($optionsResolver, $this->defaultOptions);

        $resolvedOptions = $optionsResolver->resolve($options);

        foreach ($expectedResolvedOptions as $option) {
            $this->assertArrayHasKey($option, $resolvedOptions);
        }

        $optionsResolverMock = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolver');
        $optionsResolverMock->expects($this->any())
                ->method('resolve')
                ->with($options)
                ->will($this->returnValue($resolvedOptions));

        return $optionsResolverMock;
    }

}

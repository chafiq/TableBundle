<?php

namespace EMC\TableBundle\Tests\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Table\Column\ColumnFactory;

/**
 * ColumnFactoryTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnFactoryTest extends \PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var \EMC\TableBundle\Table\Column\ColumnRegistryInterface
     */
    private $registryMock;

    /**
     *
     * @var \EMC\TableBundle\Table\Column\ColumnFactoryInterface
     */
    private $factory;
    
    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private $optionsResolver;
    
    /**
     * @var \EMC\TableBundle\Table\Column\Type\ColumnTypeInterface
     */
    private $typeMock;

    public function setUp() {
        
        $options = array();
        $this->optionsResolver = $this->getOptionsResolverMock('foo', $options);
        
        $this->typeMock = $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface');
        $this->typeMock->expects($this->any())
                            ->method('getOptionsResolver')
                            ->will($this->returnValue($this->optionsResolver));
        
        $this->registryMock = $this->getMock('EMC\TableBundle\Table\Column\ColumnRegistryInterface');
        $this->registryMock->expects($this->any())
                            ->method('getType')
                            ->with('bar')
                            ->will($this->returnValue($this->typeMock));
        
        $this->factory = new ColumnFactory($this->registryMock);
    }
    
    public function testCreate() {
        $idx = rand(1, 10);
        $column = $this->factory->create('foo', 'bar', $idx, array());
        $options = $column->getColumn()->getOptions();
        
        $this->assertArrayHasKey('_idx', $options);
        $this->assertEquals($idx, $options['_idx']);
        $this->assertArrayHasKey('_passed_options', $options);
        
    }
    
    public function testResolve() {
        $type = new Type\BarType();
        $this->assertEquals($this->typeMock, $this->factory->create('foo', $this->typeMock, 0)->getColumn()->getType());
        $this->assertEquals($this->typeMock, $this->factory->create('foo', 'bar', 0)->getColumn()->getType());
    }
    
    public function getOptionsResolverMock($name, array $options) {
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
        
        $type = new Type\BarType();
        $optionsResolver = $type->getOptionsResolver();
        $type->setDefaultOptions($optionsResolver);
        $options['name'] = $name;
        $resolvedOptions = $optionsResolver->resolve($options);
        
        foreach( $expectedResolvedOptions as $option ) {
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

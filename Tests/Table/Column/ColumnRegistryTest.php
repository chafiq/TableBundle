<?php

namespace EMC\TableBundle\Tests\Table\Column;

use EMC\TableBundle\Table\Column\ColumnRegistry;

/**
 * ColumnRegistryTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnRegistryTest extends \PHPUnit_Framework_TestCase {
 
    /**
     *
     * @var \EMC\TableBundle\Table\Column\ColumnRegistryInterface
     */
    private $registry;
    
    public function setUp() {
        $types = array(
            'service.x' => $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface'),
            'service.y' => $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface'),
            'service.z' => $this->getMock('EMC\TableBundle\Table\Column\Type\ColumnTypeInterface')
        );
        
        $types['service.x']->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('x'));
        
        $types['service.y']->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('y'));
        
        $types['service.z']->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('_'));
        
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->any())
                ->method('get')
                ->with($this->logicalOr(
                        $this->equalTo('service.x'), $this->equalTo('service.y'), $this->equalTo('service.z')
                ))
                ->will($this->returnCallback(function($name) use ($types) {
                            return $types[$name];
                        }));
        $this->registry = new ColumnRegistry(array('x' => 'service.x', 'y' => 'service.y', 'z' => 'service.z'));
        $this->registry->setContainer($container);
    }
    
    public function testGetType() {
        $this->assertInstanceOf( 'EMC\TableBundle\Table\Column\Type\ColumnTypeInterface', $this->registry->getType('x'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTypeException() {
        $this->registry->getType('p');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTypeUnmatchedException() {
        $this->registry->getType('z');
    }
    
    public function testHasType() {
        $this->assertEquals(true, $this->registry->hasType('x'));
        $this->assertEquals(false, $this->registry->hasType('p'));
    }
}

<?php

namespace EMC\TableBundle\Tests\Table\Export;

use EMC\TableBundle\Table\Export\ExportRegistry;

/**
 * ExportRegistryTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ExportRegistryTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \EMC\TableBundle\Table\Export\ExportRegistryInterface
     */
    private $registry;

    public function setUp() {
        $types = array(
            'service.x' => $this->getMock('EMC\TableBundle\Table\Export\Extension\ExportExtensionInterface'),
            'service.y' => $this->getMock('EMC\TableBundle\Table\Export\Extension\ExportExtensionInterface'),
            'service.z' => $this->getMock('EMC\TableBundle\Table\Export\Extension\ExportExtensionInterface')
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
        $this->registry = new ExportRegistry(array('x' => 'service.x', 'y' => 'service.y', 'z' => 'service.z'));
        $this->registry->setContainer($container);
    }

    public function testGet() {
        $this->assertInstanceOf('EMC\TableBundle\Table\Export\Extension\ExportExtensionInterface', $this->registry->get('x'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTypeException() {
        $this->registry->get('p');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTypeUnmatchedException() {
        $this->registry->get('z');
    }

    public function testHasType() {
        $this->assertEquals(true, $this->registry->has('x'));
        $this->assertEquals(false, $this->registry->has('p'));
    }
}

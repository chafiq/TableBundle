<?php

namespace EMC\TableBundle\Tests\Table;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Table\Table;

/**
 * TableTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableTest extends AbstractUnitTest {

    /**
     * @var \EMC\TableBundle\Table\Type\TableTypeInterface
     */
    private $typeMock;

    /**
     * @var \EMC\TableBundle\Table\TableInterface
     */
    private $table;

    /**
     * @var array
     */
    private $options;

    /**
     * @var \EMC\TableBundle\Table\Export\Extension\ExportExtensionInterface
     */
    private $exportExtensionMock;

    public function setUp() {
        $this->exportExtensionMock = $this->getMock('EMC\TableBundle\Table\Export\Extension\ExportExtensionInterface');
        $this->options = array('a' => 1, 'b' => '2', 'export' => array('e' => $this->exportExtensionMock));
        $this->typeMock = $this->getMock('EMC\TableBundle\Table\Type\TableTypeInterface');
        $this->table = new Table($this->typeMock, array(), $this->options);
    }

    public function testGetOptions() {
        $this->assertEquals($this->options, $this->table->getOptions());
    }

    public function testGetOption() {
        $this->assertEquals(1, $this->table->getOption('a'));
        $this->assertEquals('2', $this->table->getOption('b'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetOptionException() {
        $this->table->getOption('c');
    }

    public function testExport() {

        $exportMock = $this->getMock('EMC\TableBundle\Table\Export\ExportInterface');

        $this->exportExtensionMock->expects($this->once())
                ->method('export')
                ->will($this->returnValue($exportMock));
        
        $this->assertEquals($exportMock, $this->table->export('e'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExportInvalidArgumentException() {
        $this->table->export(1);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testExportUnexpectedValueException() {
        $this->table->export('c');
    }

}

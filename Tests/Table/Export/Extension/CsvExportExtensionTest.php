<?php

namespace EMC\TableBundle\Tests\Table\Export\Extension;

use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\Export\Extension\CsvExportExtension;

/**
 * CsvExportExtensionTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class CsvExportExtensionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var CsvExportExtension
     */
    private $extension;

    public function setUp() {
        $this->extension = new CsvExportExtension(';', '"', '\\');
        
        $this->assertEquals('fa fa-file-text-o', $this->extension->getIcon());
        $this->assertEquals('csv', $this->extension->getName());
        $this->assertEquals('csv', $this->extension->getFileExtension());
        $this->assertEquals('CSV', $this->extension->getText());
    }

    public function testExport() {

        $view = new TableView();
        $view->setData(array(
            'caption' => 'abc',
            'thead' => array(
                array('title' => 'a'),
                array('title' => 'b'),
            ),
            'tbody' => array(
                array( 'data' => array(array('value' => 1), array('value' => '2'))),
                array( 'data' => array(array('value' => 7.5), array('value' => 'test'))),
            ),
        ));
        
        $export = $this->extension->export($view);
        $this->assertInstanceOf('EMC\TableBundle\Table\Export\ExportInterface', $export);
        $this->assertEquals($this->extension->getContentType(), $export->getContentType());
        $this->assertEquals($this->extension->getFileExtension(), $export->getFileExtension());
        $this->assertEquals("a;b\n1;2\n7.5;test\n", file_get_contents($export->getFile()->getPathname()));
    }

}

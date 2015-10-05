<?php

namespace EMC\TableBundle\Tests\Table\Export\Extension;

use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\Export\Extension\XlsExportExtension;

/**
 * XlsExportExtensionTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class XlsExportExtensionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var XlsExportExtension
     */
    private $extension;

    /**
     * @var string
     */
    private $content;

    public function setUp() {
        $twigMock = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $this->extension = new XlsExportExtension($twigMock, 'template');

        $this->content = 'my beautiful template content <xls />';
        $twigMock->expects($this->once())
                ->method('render')
                ->will($this->returnValue($this->content));
        $this->assertEquals('fa fa-file-excel-o', $this->extension->getIcon());
        $this->assertEquals('xls', $this->extension->getName());
        $this->assertEquals('xls', $this->extension->getFileExtension());
        $this->assertEquals('EXCEL', $this->extension->getText());
    }

    public function testExport() {



        $view = new TableView();
        $view->setData(array(
            'caption' => 'abc'
        ));

        $export = $this->extension->export($view);
        $this->assertInstanceOf('EMC\TableBundle\Table\Export\ExportInterface', $export);
        $this->assertEquals($this->extension->getContentType(), $export->getContentType());
        $this->assertEquals($this->extension->getFileExtension(), $export->getFileExtension());
        $this->assertEquals($this->content, file_get_contents($export->getFile()->getPathname()));
    }

}

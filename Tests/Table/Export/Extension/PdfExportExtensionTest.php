<?php

namespace EMC\TableBundle\Tests\Table\Export\Extension;

use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\Export\ExportInterface;
use EMC\TableBundle\Table\Export\Extension\PdfExportExtension;

/**
 * PdfExportExtensionTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class PdfExportExtensionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var PdfExportExtension
     */
    private $extension;

    public function setUp() {
        $content = '<table>'
                . '<thead><tr><th>a</th><th>b</th></tr></thead>'
                . '<tbody>'
                . '<tr><td>1<td><td>foo</td></tr>'
                . '<tr><td>1<td><td>bar</td></tr>'
                . '</tbody>'
                . '</table>';

        $twigMock = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $options = array(
            'page-size' => 'A4',
            'orientation' => 'Portrait',
            'title' => 'Export [caption] [now]',
            'filename' => 'Export [caption] [now]',
            'timeout' => 10
        );
        $this->extension = new PdfExportExtension($twigMock, 'tpl', 'wkhtmltopdf', $options);

        $twigMock->expects($this->once())
                ->method('render')
                ->will($this->returnValue($content));

        $this->assertEquals('fa fa-file-pdf-o', $this->extension->getIcon());
        $this->assertEquals('pdf', $this->extension->getName());
        $this->assertEquals('pdf', $this->extension->getFileExtension());
        $this->assertEquals('PDF', $this->extension->getText());
    }

    public function testExport() {
        $view = new TableView();
        $view->setData(array('caption' => 'test'));
        $export = $this->extension->export($view);
        $this->assertInstanceOf('EMC\TableBundle\Table\Export\ExportInterface', $export);
        $this->assertEquals($this->extension->getContentType(), $export->getContentType());
        $this->assertEquals($this->extension->getFileExtension(), $export->getFileExtension());
        $this->assertEquals('application/pdf', mime_content_type($export->getFile()->getPathname()));
    }

}

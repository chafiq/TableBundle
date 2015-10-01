<?php

namespace EMC\TableBundle\Tests\Table\Export\Extension;

use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Table\Export\Extension\PdfExportExtension;

/**
 * PdfExportExtensionTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class PdfExportExtensionTest extends AbstractUnitTest {

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

        $dir = getcwd() . '/Tests/build/tmp';
        $bin = sprintf('docker run --rm -t -i -v %s:%s chafiq/wkhtmltox:1.0 wkhtmltopdf', $dir, $dir);

        $options = array(
            'page-size' => 'A4',
            'orientation' => 'Portrait',
            'title' => 'Export [caption] [now]',
            'filename' => 'Export [caption] [now]',
            'timeout' => 10,
            'dir' => $dir
        );

        $this->extension = new PdfExportExtension($twigMock, 'tpl', $bin, $options);

        $twigMock->expects($this->any())
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

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildPdfRuntimeException() {
        $input = tempnam('/tmp', 'phpunit-test-');
        $output = tempnam('/tmp', 'phpunit-test-');
        $this->invokeSetter($this->extension, 'bin', '/tmp/' . md5(rand()));
        $this->invokeMethod($this->extension, 'buildPdf', array($input, $output, array('caption'=>'test')));
    }
    
    public function testGetProcess() {
        $cmd = '#test process command';
        /* @var $process \Symfony\Component\Process\Process */
        $process = $this->invokeMethod($this->extension, 'getProcess', array($cmd));
        $this->assertEquals($cmd, $process->getCommandLine());
        $this->assertEquals(10, $process->getTimeout());
    }
    
    public function testTempnam() {
        $filename = $this->invokeMethod($this->extension, 'tempnam', array('ext'));
        $this->assertTrue(file_exists($filename));
        $this->assertEquals('.ext', substr($filename, -4));
    }
    
}

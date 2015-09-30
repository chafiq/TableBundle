<?php

namespace EMC\TableBundle\Tests\Table\Export;

use EMC\TableBundle\Table\Export\Export;

/**
 * ExportTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ExportTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \EMC\TableBundle\Table\Export\ExportInterface
     */
    private $export;

    /**
     * @var \SplFileInfo
     */
    private $file;

    public function setUp() {
        $tempnam = tempnam('/tmp', 'phpunit-');
        $this->file = new \SplFileInfo($tempnam);
        $this->export = new Export($this->file, 'content/type', 'file.ext', 'ext');
    }

    public function testGetFile() {
        $this->assertEquals($this->file, $this->export->getFile());
    }

    public function testGetFilename() {
        $this->assertEquals('file.ext', $this->export->getFilename());
    }

    public function testGetFileExtension() {
        $this->assertEquals('ext', $this->export->getFileExtension());
    }

    public function testGetContentType() {
        $this->assertEquals('content/type', $this->export->getContentType());
    }

    public function testDestruct(){
        unset($this->export);
        $this->assertFalse(file_exists($this->file->getPathname()));
    }
}

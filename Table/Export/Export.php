<?php

namespace EMC\TableBundle\Table\Export;

/**
 * Export
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class Export implements ExportInterface {

    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $fileExtension;

    function __construct(\SplFileInfo $file, $contentType, $filename = null, $fileExtension = null) {
        $this->file = $file;
        $this->filename = $filename ? : $file->getBasename();
        $this->contentType = $contentType;
        $this->fileExtension = $fileExtension ? : $file->getExtension();
    }

    public function __destruct() {
        unlink($this->getFile()->getPathname());
    }

    /**
     * {@inheritdoc}
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension() {
        return $this->fileExtension;
    }

}

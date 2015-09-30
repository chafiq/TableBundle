<?php

namespace EMC\TableBundle\Table\Export;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ExportInterface {

    /**
     * @return \SplFileInfo the SplFileInfo of the exported resource
     */
    public function getFile();

    /**
     * @return string public file name 
     */
    public function getFilename();

    /**
     * @return string Extension Content-type
     */
    public function getContentType();

    /**
     * @return string File extension
     */
    public function getFileExtension();
}

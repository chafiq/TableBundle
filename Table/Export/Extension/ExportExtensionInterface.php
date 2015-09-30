<?php

namespace EMC\TableBundle\Table\Export\Extension;

use EMC\TableBundle\Table\TableView;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ExportExtensionInterface {
    
    /**
     * @return string Extension name
     */
    public function getName();
    
    /**
     * @return string Extension text appears in the button dropdown HTML
     */
    public function getText();
    
    /**
     * @return string Extension icon appears in the button dropdown HTML
     */
    public function getIcon();
    
    /**
     * @return string Extension Content-type
     */
    public function getContentType();
    
    /**
     * @return string File extension
     */
    public function getFileExtension();
    
    /**
     * Render and export the table
     * @param \EMC\TableBundle\Table\TableView $view
     * @param string|null $template
     * @param array $options
     * @return \EMC\TableBundle\Table\Export\ExportInterface
     */
    public function export(TableView $view, $template=null, array $options=array());
}

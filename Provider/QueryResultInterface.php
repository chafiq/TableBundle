<?php

namespace EMC\TableBundle\Provider;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface QueryResultInterface {
    
    /**
     * @return array Data rows
     */
    public function getRows();
    
    /**
     * @return int Total rows count
     */
    public function getCount();
}

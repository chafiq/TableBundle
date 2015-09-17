<?php

namespace EMC\TableBundle\Table;

/**
 * TableView
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableView {
    /**
     * @var array
     */
    private $data;
    
    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }
}

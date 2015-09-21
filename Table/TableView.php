<?php

namespace EMC\TableBundle\Table;

/**
 * TableView
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableView {
    
    /**
     * View's data. They are populated in the TableTypeInterface::buildView.<br/>
     * This $data must contains all template needs.<br/>
     * @var array
     */
    private $data;
    
    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data) {
        $this->data = $data;
    }
}

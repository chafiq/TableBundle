<?php

namespace EMC\TableBundle\Column;

/**
 * Icon Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class Icon extends Text implements ColumnInterface {
    
    /**
     * @var string
     */
    protected $icon;
    
    public function getIcon() {
        return $this->icon;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function getExtension() {
        return 'icon';
    }

    public function getView($data) {
        return array(
            'icon'  => $this->icon
        );
    }

}
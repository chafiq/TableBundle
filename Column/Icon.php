<?php

namespace EMC\TableBundle\Column;

/**
 * Description of Button
 *
 * @author emc
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
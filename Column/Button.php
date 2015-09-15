<?php

namespace EMC\TableBundle\Column;

/**
 * Description of Button
 *
 * @author emc
 */
class Button extends Text implements ColumnInterface {
    
    /**
     * @var string
     */
    protected $text;
    
    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var string
     */
    protected $icon;
    
    
    public function getText() {
        return $this->text;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function getExtension() {
        return 'button';
    }

    public function getView($data) {
        return array(
            'text'  => $this->text,
            'title' => $this->title,
            'icon'  => $this->icon
        );
    }

}
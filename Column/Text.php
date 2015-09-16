<?php

namespace EMC\TableBundle\Column;

/**
 * Text Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class Text extends Base implements ColumnInterface, TextInterface {
    
    /**
     * @var boolean
     */
    protected $searchable;
    
    /**
     * @var boolean
     */
    protected $sortable;
    
    /**
     * @var callable
     */
    protected $formatter;
    
    /**
     * @var callable
     */
    protected $footer;
    
    public function isSearchable() {
        return $this->searchable;
    }

    public function isSortable() {
        return $this->sortable;
    }

    public function getFormatter() {
        return $this->formatter;
    }

    public function getFooter() {
        return $this->footer;
    }

    public function setSearchable($searchable) {
        $this->searchable = $searchable;
        return $this;
    }

    public function setSortable($sortable) {
        $this->sortable = $sortable;
        return $this;
    }

    public function setFormatter($formatter) {
        if ( is_string($formatter) ) {
            $this->formatter = function( $data ) use ($formatter) { return sprintf( $formatter, $data ); };
        } elseif (is_callable($formatter) || is_null($formatter)) {
            $this->formatter = $formatter;
        } else {
            throw new \InvalidArgumentException('$formatter callable|string|null expected ' . get_class($formatter) . ' found');
        }
        return $this;
    }

    public function setFooter(callable $footer = null) {
        $this->footer = $footer;
        return $this;
    }
    
    public function format(array $data) {
        if (is_null($data)) {
            return '';
        }
        
        if (is_callable($this->formatter)) {
            return call_user_func_array($this->formatter, $data);
        }
        
        if ( count($data) === 1 ) {
            return reset($data);
        }
        
        throw new \UnexpectedValueException;
        
    }
    
    public function getExtension() {
        return 'text';
    }

    public function getView($data) {
        return array(
            'text' => $this->format($data)
        );
    }

}

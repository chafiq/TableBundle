<?php

namespace EMC\TableBundle\Column;

/**
 * Description of Button
 *
 * @author emc
 */
class Anchor extends Button implements ColumnInterface {
    
    /**
     * @var string
     */
    protected $route;
    
    /**
     * @var array
     */
    protected $args;
    
    public function getRoute() {
        return $this->route;
    }

    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }
    
    public function getArgs() {
        return $this->args;
    }

    public function setArgs($args) {
        $this->args = $args;
    }

    public function getExtension() {
        return 'anchor';
    }

    public function getView($data) {
        return array(
            'route' => $this->route,
            'params'=> $this->resolveParams($data),
            'text'  => is_string($this->text) && strlen($this->text) > 0 ? $this->text : $this->format($data),
            'title' => $this->title,
            'icon'  => $this->icon
        );
    }
    
    private function resolveParams(array $data) {
        $params = $this->getParams();
        foreach( $params as &$param ) {
            $param = $data[$param];
        }
        unset($param);
        
        if ( !is_array($this->args) || count($this->args) === 0 ) {
            return $params;
        }
        
        return array_merge($params, $this->args);
    }
}

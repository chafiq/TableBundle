<?php

namespace EMC\TableBundle\Column;

/**
 * ColumnBuilder
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnBuilder implements ColumnBuilderInterface {
    
    /**
     * @var ColumnTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    function __construct(ColumnTypeInterface $type, $options) {
        $this->type = $type;
        $this->options = $options;
    }
    
    public function getColumn() {
        return new Column($this->type, $this->options);
    }
    
}

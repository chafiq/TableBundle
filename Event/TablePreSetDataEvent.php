<?php

namespace EMC\TableBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use EMC\TableBundle\Table\TableTypeInterface;

/**
 * TablePreSetDataEvent
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TablePreSetDataEvent extends Event {
    
    const NAME = 'table.pre_set_data';

    /**
     * @var TableTypeInterface
     */
    private $type;

    /**
     * @var mixed
     */
    private $data;
    
    /**
     * @var array
     */
    private $options;
    
    function __construct(TableTypeInterface $type, $data = null, array $options = array()) {
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
    }

    public function getType() {
        return $this->type;
    }

    public function getData() {
        return $this->data;
    }

    public function getOptions() {
        return $this->options;
    }
}

<?php

namespace EMC\TableBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use EMC\TableBundle\Table\TableTypeInterface;

/**
 * Description of TablePreSetDataEvent
 *
 * @author emc
 */
class TablePreSetDataEvent extends Event {
    
    const NAME = 'table.pre_set_data';

    /**
     * @var TableTypeInterface
     */
    private $type;

    /**
     * @var string
     */
    private $tableId;

    /**
     * @var mixed
     */
    private $data;
    
    /**
     * @var array
     */
    private $options;
    
    function __construct(TableTypeInterface $type, $tableId, $data = null, array $options = array()) {
        $this->type = $type;
        $this->tableId = $tableId;
        $this->data = $data;
        $this->options = $options;
    }

    public function getType() {
        return $this->type;
    }

    public function getTableId() {
        return $this->tableId;
    }

    public function getData() {
        return $this->data;
    }

    public function getOptions() {
        return $this->options;
    }
}

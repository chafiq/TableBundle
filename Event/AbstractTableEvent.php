<?php

namespace EMC\TableBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use EMC\TableBundle\Table\TableInterface;

/**
 * AbstractTableEvent
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class AbstractTableEvent extends Event {

    /**
     * @var TableInterface
     */
    private $table;

    /**
     * @var array|null
     */
    private $data;

    /**
     * @var array
     */
    private $options;

    function __construct(TableInterface $table, array $data = null, array $options = array()) {
        $this->table = $table;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * @return TableInterface
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @return array|null
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

}

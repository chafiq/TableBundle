<?php

namespace EMC\TableBundle\Listener;

use EMC\TableBundle\Event\TablePreSetDataEvent;
use EMC\TableBundle\Event\TablePostSetDataEvent;
use EMC\TableBundle\Session\TableSessionInterface;

/**
 * TableListener
 * 
 * This class store the meta-data of tables while "table.pre_set_data" event is trigged
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableListener {
    
    /**
     * @var TableSessionInterface
     */
    private $session;
    
    function __construct(TableSessionInterface $session) {
        $this->session = $session;
    }

    public function onPreSetData(TablePreSetDataEvent $event) {
        $this->session->store($event->getType(), $event->getTableId(), $event->getData(), $event->getOptions());
    }
    
    public function onPostSetData(TablePostSetDataEvent $event) {
    }
}

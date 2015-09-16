<?php

namespace EMC\TableBundle\Listener;

use EMC\TableBundle\Event\TablePreSetDataEvent;
use EMC\TableBundle\Event\TablePostSetDataEvent;
use EMC\TableBundle\Session\TableSessionInterface;
/**
 * Description of TableListener
 *
 * @author emc
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

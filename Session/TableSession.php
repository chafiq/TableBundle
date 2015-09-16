<?php

namespace EMC\TableBundle\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use EMC\TableBundle\Table\TableTypeInterface;

/**
 * TableSession manage tables in the session
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableSession implements TableSessionInterface {
    
    /**
     * @var SessionInterface
     */
    private $session;
    
    function __construct(SessionInterface $session) {
        $this->session = $session;
    }
    
    public function restore($tableId) {
        if ( ($tables = $this->session->get('tables', null)) === null || !isset($tables[$tableId]) ) {
            throw new \InvalidArgumentException;
        }
        
        return $tables[$tableId];
    }

    public function store(TableTypeInterface $type, $tableId, $data = null, array $options = array()) {
        $tables = $this->session->get('tables');
        
        $tables[$tableId] = array(
            'class'     => get_class($type),
            'data'      => $data,
            'options'   => $options
        );
        
        $this->session->set('tables', $tables);
    }
}

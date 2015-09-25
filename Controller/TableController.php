<?php

namespace EMC\TableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ajax Handler Controller
 * This controller handle ajax requests sent by client side (@see EMCTable.js)
 * 
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableController extends Controller {
    
    /**
     * Access point for pagination, filtering and subtables loading.<br/>
     * All the table action in the client side are performed here.<br/>
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request) {
        /* @var $factory \EMC\TableBundle\Table\TableFactoryInterface */
        $factory = $this->get('table.factory');
        
        $tableId = $request->get('tid');
        if ( !is_string($tableId) || strlen($tableId) === 0 ) {
            throw $this->createNotFoundException();
        }
        
        $params = $request->get('params', array());
        $isSubtable = (bool) $request->get('subtable');
        
        $table = $factory->restore($tableId, $params);
        $table->handleRequest($request);
        
        return $this->render('EMCTableBundle:Table:' . ($isSubtable ? 'index' : 'fragment') . '.html.twig', array('table' => $table->getTable()));
    }
}

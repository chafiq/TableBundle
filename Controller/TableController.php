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
    public function indexAction(Request $request) {
        /* @var $factory \EMC\TableBundle\Table\TableFactoryInterface */
        $factory = $this->get('table.factory');
        
        $tableId = $request->get('tid');
        if ( !is_string($tableId) || strlen($tableId) === 0 ) {
            return $this->createNotFoundException();
        }
        
        $table = $factory->restore($tableId);
        $table->handleRequest($request);
        
        return $this->render('EMCTableBundle:Table:index.html.twig', array('table' => $table->getTable()));
    }
}

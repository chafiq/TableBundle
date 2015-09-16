<?php

namespace EMC\TableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

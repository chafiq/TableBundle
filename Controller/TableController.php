<?php

namespace EMC\TableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TableController extends Controller {
    public function indexAction(Request $request) {
        /* @var $factory \EMC\TableBundle\Table\TableFactoryInterface */
        $factory = $this->get('table.factory');
        
        $uid = $request->get('uid');
        if ( !is_string($uid) || strlen($uid) === 0 ) {
            return $this->createNotFoundException();
        }
        
        $table = $factory->restore($uid);
        $table->handleRequest($request);
        
        return $this->render('EMCTableBundle:Table:index.html.twig', array('table' => $table->getTable()));
    }
}

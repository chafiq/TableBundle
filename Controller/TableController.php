<?php

namespace EMC\TableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use EMC\TableBundle\Table\TableFactory;

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
        
        $table = $this->getTable($request);
        
        $isSubtable = (bool) $request->get('subtable');
        
        return $this->render('EMCTableBundle:Table:' . ($isSubtable ? 'index' : 'fragment') . '.html.twig', array('table' => $table->getView()));
    }
    
    public function selectAction(Request $request) {
        $table = $this->getTable($request, TableFactory::MODE_SELECTION);
        return new JsonResponse($table->getView()->getData());
    }
    
    public function exportAction(Request $request) {
        $table = $this->getTable($request, TableFactory::MODE_EXPORT);
        
        /* @var $export \EMC\TableBundle\Table\Export\ExportInterface */
        $export = $table->export($request->get('type'));
        
        $resource = fopen($export->getFile()->getPathname(), 'r');
        
        $content = stream_get_contents($resource);
        $response = new Response($content);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', $export->getContentType());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $export->getFilename() . '.'. $export->getFileExtension() . '"');

        return $response;
    }
    
    /**
     * Return table object
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \EMC\TableBundle\Table\TableInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getTable(Request $request, $mode=TableFactory::MODE_NORMAL) {
        /* @var $factory \EMC\TableBundle\Table\TableFactoryInterface */
        $factory = $this->get('table.factory');
        
        $tableId = $request->get('tid');
        if ( !is_string($tableId) || strlen($tableId) === 0 ) {
            throw $this->createNotFoundException();
        }
        
        $params = $request->get('params', array());
        
        $table = $factory->restore($tableId, $params, $mode);
        $table->handleRequest($request);
        
        return $table->getTable();
    }
}

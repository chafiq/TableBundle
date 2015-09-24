<?php

namespace EMC\TableBundle\Tests\Session;

use EMC\TableBundle\Tests\AbstractUnitTest;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use EMC\TableBundle\Session\TableSession;

/**
 * TableSessionTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableSessionTest extends AbstractUnitTest {
    /**
     *
     * @var \EMC\TableBundle\Session\TableSessionInterface
     */
    private $tableSession;
    
    /**
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;
    
    public function setUp() {
        $this->session = new Session(new MockFileSessionStorage());
        $this->tableSession = new TableSession($this->session);
    }
    
    public function testStoreAndRestore() {
        
        $type = $this->getMock('EMC\TableBundle\Table\Type\TableTypeInterface');
        $data = array(array(1), array(2));
        $options = array('_tid' => 'abc', '_passed_options' => array('x'=>3, 'y'=>'z'));
        $this->tableSession->store($type, $data, $options);
        
        $expected = array($options['_tid'] => array(
            'class' => get_class($type),
            'data'  => $data,
            'options'=> $options['_passed_options']
        ));
        
        $this->assertEquals($expected, $this->session->get('tables'));
        $this->assertEquals($expected[$options['_tid']], $this->tableSession->restore($options['_tid']));
    }
        
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRestoreException() {
        $this->tableSession->restore('xxx');
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testStoreException() {
        $type = $this->getMock('EMC\TableBundle\Table\Type\TableTypeInterface');
        $data = array(array(1), array(2));
        $options = array('x'=>3, 'y'=>'z');
        $this->tableSession->store($type, $data, $options);
    }
}

<?php

namespace EMC\TableBundle\Tests\Listener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use EMC\TableBundle\Session\TableSession;
use EMC\TableBundle\Event\TablePreSetDataEvent;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * TableListenerTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableListenerTest extends WebTestCase {

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

    public function testOnPreSetData() {

        $table = $this->getMock('EMC\TableBundle\Table\TableInterface');
        $type = $this->getMock('EMC\TableBundle\Table\Type\TableTypeInterface');
        $data = array(array('a' => 1, 'b' => 2));
        $options = array('x' => '_', 'y' => array(), '_tid' => 'xxx', '_passed_options' => array('x' => '_', 'y' => array()));

        $table->expects($this->exactly(3))
                ->method('getType')
                ->will($this->returnValue($type));

        $table->expects($this->once())
                ->method('getOptions')
                ->will($this->returnValue($options));

        $table->expects($this->once())
                ->method('getColumns')
                ->will($this->returnValue(array()));

        $event = new TablePreSetDataEvent($table, $data, $options);

        $client = $this->createClient();
        $client->getContainer()->get('event_dispatcher')->dispatch(TablePreSetDataEvent::NAME, $event);

        $expected = array( 'xxx' => array(
                'class' => get_class($type),
                'data' => array(
                    0 => array(
                        'a' => 1,
                        'b' => 2,
                    ),
                ),
                'options' => array(
                    'x' => '_',
                    'y' => array(),
                ),
            ),
        );

        $this->assertEquals($expected, $client->getContainer()->get('session')->get('tables'));
    }

}

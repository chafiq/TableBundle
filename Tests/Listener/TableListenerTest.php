<?php

namespace EMC\TableBundle\Tests\Listener;

use EMC\TableBundle\Tests\AbstractUnitTest;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use EMC\TableBundle\Session\TableSession;

/**
 * TableListenerTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableListenerTest extends AbstractUnitTest {

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
    }
}

<?php

namespace EMC\TableBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use EMC\TableBundle\Tests\Table\Type\MockType;
use EMC\TableBundle\Tests\Provider\QueryBuilderMock;

class TableControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();

        $table = $client->getContainer()->get('table.factory')
                ->create(new MockType())
                ->getTable();
        
        $session = $client->getContainer()->get('session');
        $tables = $session->get('tables');
        
        $this->assertArrayHasKey($table->getOption('_tid'), $tables);
        
        $query = array(
            'tid' => $table->getOption('_tid'),
            'params' => array(
                'page' => 2,
                'limit' => 5,
                'sort' => 0,
                'filter' => ''
            )
        );
        
        $crawler = $client->request('GET', '/_table?' . http_build_query($query));
        $expectedResponse = '<div><table><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr><tr><td><span class="column-text column-id">0</span></td><td><span class="column-text column-name">1</span></td></tr></table><ul class="pagination pagination-sm"><li class="previous"><span class="hidden-xs">&laquo;</span></li><li class="previous"><span class="hidden-xs">&lsaquo;</span></li><li class="current active" data-page="1"><a>1</a></li><li class="page"><a data-page="2">2</a></li><li class="page"><a data-page="3">3</a></li><li class="next"><a class="hidden-xs" data-page="2">&rsaquo;</a></li><li class="next"><a class="hidden-xs" data-page="3">&raquo;</a></li></ul></div>';
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectedResponse, $client->getResponse()->getContent());
    }

    public function testIndex404() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/_table');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

}

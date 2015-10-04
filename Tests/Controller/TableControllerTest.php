<?php

namespace EMC\TableBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use EMC\TableBundle\Tests\Table\Type\MockType;

class TableControllerTest extends WebTestCase {

    public function testIndexAction() {
        $client = static::createClient();

        $table = $client->getContainer()->get('table.factory')
                ->create(new MockType())
                ->getTable();
        
        $session = $client->getContainer()->get('session');
        $tables = $session->get('tables');
        
        $this->assertArrayHasKey($table->getOption('_tid'), $tables);
        
        $query = array(
            'tid' => $table->getOption('_tid'),
            'params' => array(),
            'query' => array()
        );
        
        $client->request('GET', '/_table?' . http_build_query($query));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('c534e285d35ef457b5173c577d7462e8e0269a13', sha1($client->getResponse()->getContent()));
    }

    public function testIndexAction404() {
        $client = static::createClient();
        $client->request('GET', '/_table');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testExportAction() {
        $client = static::createClient();

        $table = $client->getContainer()->get('table.factory')
                ->create(new MockType(), null, array('export' => array('csv')))
                ->getTable();
        
        $session = $client->getContainer()->get('session');
        $tables = $session->get('tables');
        
        $this->assertArrayHasKey($table->getOption('_tid'), $tables);
        
        $query = array(
            'tid' => $table->getOption('_tid'),
            'type' => 'csv',
            'params' => array(),
            'query' => array()
        );
        
        $client->request('GET', '/_table/export?' . http_build_query($query));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('bece19a045cd5f6e10a59eec5f7d24d4b5f55415', sha1($client->getResponse()->getContent()));
    }
    
    public function testSelectAction() {
        $client = static::createClient();

        $table = $client->getContainer()->get('table.factory')
                ->create(new MockType(), null, array(
                    'allow_select' => true,
                    'rows_params' => array('id', 'name')
                ))
                ->getTable();
        
        $session = $client->getContainer()->get('session');
        $tables = $session->get('tables');
        
        $this->assertArrayHasKey($table->getOption('_tid'), $tables);
        
        $query = array(
            'tid' => $table->getOption('_tid'),
            'params' => array(),
            'query' => array()
        );
        
        $client->request('GET', '/_table/select?' . http_build_query($query));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('db46f401655530cfb1983ec689e5900b275797ee', sha1($client->getResponse()->getContent()));
    }
}

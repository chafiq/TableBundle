<?php

namespace EMC\TableBundle\Tests\Profiler\DataCollector;

use EMC\TableBundle\Tests\Table\TableAbstractTest;
use EMC\TableBundle\Profiler\DataCollector\TableDataCollector;
use Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter;

/**
 * TableDataCollectorTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableDataCollectorTest extends TableAbstractTest {

    public function testCollectConfig() {
        $this->builder->add('id', 'text');
        $this->builder->add('name', 'text', array(
            'format' => '#%s!',
            'title' => 'name'
        ));
        $table = $this->builder->getTable();

        $valueExporter = new ValueExporter;
        $dataCollector = new TableDataCollector($valueExporter);
        $this->resolvedOptions['_passed_options'] = array('a' => 1, 'b' => 'x');
        $dataCollector->collectConfig($table, null, $this->resolvedOptions);

        $columns = $this->builder->getColumns();

        $expected = array(
            'test' => array(
                'id' => 'foo',
                'name' => 'foo',
                'type' => 'foo',
                'type_class' => 'EMC\TableBundle\Tests\Table\Type\FooType',
                'passed_options' => array('a' => 1, 'b' => 'x'),
                'resolved_options' => array(
                    'allow_select' => 'false',
                    'attrs' => '[]',
                    'caption' => '',
                    'data' => 'null',
                    'data_provider' => $valueExporter->exportValue($this->dataProvider),
                    'default_sorts' => '[]',
                    'limit' => '10',
                    'name' => 'foo',
                    'params' => '[]',
                    'route' => '_table',
                    'rows_pad' => 'true',
                    'rows_params' => '[]',
                    'subtable' => 'null',
                    'subtable_options' => '[]',
                    'subtable_params' => '[]',
                    'subtable_params' => '[]',
                    'export' => '[]',
                    'export_route' => '_table_export',
                    'select_route' => '_table_select',
                ),
                'columns' => array(
                    'id' => array(
                        'id' => 'id',
                        'name' => 'id',
                        'type' => NULL,
                        'type_class' => get_class($columns['id']->getType()),
                        'passed_options' => array(
                            'name' => 'id',
                            'params' => '[0 => id]',
                            'allow_sort' => 'false',
                        ),
                        'resolved_options' => array(
                            'name' => 'id',
                            'title' => '',
                            'params' => '[0 => id]',
                            'attrs' => '[]',
                            'data' => 'null',
                            'default' => 'null',
                            'format' => 'null',
                            'allow_sort' => 'false',
                            'allow_filter' => 'false',
                            'width' => 'null'
                        ),
                    ),
                    'name' => array(
                        'id' => 'name',
                        'name' => 'name',
                        'type' => NULL,
                        'type_class' => get_class($columns['name']->getType()),
                        'passed_options' => array(
                            'format' => '#%s!',
                            'title' => 'name',
                            'name' => 'name',
                            'params' => '[0 => name]',
                            'allow_sort' => '[0 => name, 1 => id]',
                            'allow_filter' => 'true',
                        ),
                        'resolved_options' => array(
                            'name' => 'name',
                            'title' => 'name',
                            'params' => '[0 => name]',
                            'attrs' => '[]',
                            'data' => 'null',
                            'default' => 'null',
                            'format' => '#%s!',
                            'allow_sort' => '[0 => name, 1 => id]',
                            'allow_filter' => 'true',
                            'width' => 'null'
                        ),
                    ),
                ),
            ),
        );
        $this->assertEquals($expected, $dataCollector->getTables());
    }

    public function testCollectData() {
        $this->builder->add('id', 'text');
        $this->builder->add('name', 'text', array(
            'format' => '#%s!',
            'title' => 'name'
        ));
        $table = $this->builder->getTable();

        $valueExporter = new ValueExporter;
        $dataCollector = new TableDataCollector($valueExporter);
        $this->resolvedOptions['_passed_options'] = array('a' => 1, 'b' => 'x');
        $dataCollector->collectData($table, null, $this->resolvedOptions);

        $tables = $dataCollector->getTables();

        $expected = array(
            'query' => '[page => 1, sort => 0, limit => 10, filter => null]',
            'total' => 23,
            'rows 0' => '[id => 1, name => Aquitaine]',
            'rows 1' => '[id => 2, name => Auvergne]',
            'rows 2' => '[id => 3, name => Bourgogne]',
            'rows 3' => '[id => 4, name => Bretagne]',
            'rows 4' => '[id => 5, name => Centre]',
            'rows 5' => '[id => 6, name => Champagne Ardenne]',
            'rows 6' => '[id => 7, name => Corse]',
            'rows 7' => '[id => 8, name => DOM/TOM]',
            'rows 8' => '[id => 9, name => Franche Comté]',
            'rows 9' => '[id => 10, name => Ile de France]',
            'rows 10' => '[id => 11, name => Languedoc Roussillon]',
            '...' => '',
            'rows 20' => '[id => 22, name => Rhône Alpes]',
            'rows 21' => '[id => 23, name => Alsace]',
            'rows 22' => '[id => 24, name => Basse-Normandie]',
        );
        $this->assertEquals($expected, $tables['test']['query_result']);
    }

}

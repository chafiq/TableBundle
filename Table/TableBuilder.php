<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Event\TablePreSetDataEvent;
use EMC\TableBundle\Event\TablePostSetDataEvent;
use EMC\TableBundle\Table\Column\ColumnFactoryInterface;
use EMC\TableBundle\Provider\QueryConfig;
use EMC\TableBundle\Provider\QueryResult;
use EMC\TableBundle\Table\Type\TableTypeInterface;

/**
 * TableBuilder
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableBuilder implements TableBuilderInterface {

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface 
     */
    private $eventDispatcher;

    /**
     * @var ColumnFactoryInterface
     */
    private $factory;

    /**
     * @var TableTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $columns;

    function __construct(ObjectManager $entityManager, EventDispatcherInterface $eventDispatcher, ColumnFactoryInterface $factory, TableTypeInterface $type, array $data = null, array $options = array()) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->factory = $factory;
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
        $this->columns = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getData() {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(Request $request) {
        $this->options['_query']['limit'] = (int) $request->get('_limit', $this->options['limit']);
        $this->options['_query']['page'] = (int) $request->get('_page', 1);
        $this->options['_query']['sort'] = (int) $request->get('_sort', $this->options['default_sorts']);
        $this->options['_query']['filter'] = $request->get('_filter', '');
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $type, array $options = array()) {
        
        if ( isset($this->columns[$name]) ) {
            throw new \InvalidArgumentException('Column name "' . $name . '" already exists.');
        }
        
        $this->columns[$name] = $this->factory->create($name, $type, $options)->getColumn();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create() {
        $table = new Table($this->type, $this->columns, $this->options);

        $event = new TablePreSetDataEvent($table, $this->data, $this->options);
        $this->eventDispatcher->dispatch(TablePreSetDataEvent::NAME, $event);

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable() {
        $table = $this->create();

        $table->setData( $this->getQueryResult($table) );

        $event = new TablePostSetDataEvent($table, $this->data, $this->options);
        $this->eventDispatcher->dispatch(TablePostSetDataEvent::NAME, $event);

        return $table;
    }
    
    public function getQueryConfig(TableInterface $table) {
        $queryConfig = new QueryConfig();
        $this->type->buildQuery($queryConfig, $table, $this->options);
        return $queryConfig;
    }

    public function getQueryResult(TableInterface $table) {
        
        $queryConfig = $this->getQueryConfig($table);
        
        if (is_array($this->data)) {
            return new QueryResult($this->data, 0);
        } else {
            $queryBuilder = $this->type->getQueryBuilder($this->entityManager, $this->options['params']);

            /* @var $dataProvider \EMC\TableBundle\Provider\DataProviderInterface */
            $dataProvider = $this->options['data_provider'];

            return $dataProvider->find($queryBuilder, $queryConfig);
        }
        
        return null;
    }
}

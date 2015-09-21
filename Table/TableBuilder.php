<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Event\TablePreSetDataEvent;
use EMC\TableBundle\Event\TablePostSetDataEvent;
use EMC\TableBundle\Column\ColumnFactoryInterface;
use EMC\TableBundle\Provider\QueryConfig;

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

        $this->options['_query'] = array(
            'route' => $options['route'],
            'page' => 1,
            'sort' => $options['default_sorts'],
            'limit' => $options['limit'],
            'filter' => null
        );

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

        $queryBuilder = $this->type->getQueryBuilder($this->entityManager, $this->options['params']);
        $queryConfig = new QueryConfig();
        $this->type->buildQuery($queryConfig, $table, $this->options);

        /* @var $dataProvider \EMC\TableBundle\Provider\DataProviderInterface */
        $dataProvider = $this->options['data_provider'];

        $data = $dataProvider->find($queryBuilder, $queryConfig);
        $table->setData($data);

        $event = new TablePostSetDataEvent($table, $this->data, $this->options);
        $this->eventDispatcher->dispatch(TablePostSetDataEvent::NAME, $event);

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $type, array $options = array()) {
        $this->columns[] = $this->factory->create($name, $type, count($this->columns), $options)->getColumn();
        return $this;
    }

}

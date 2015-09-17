<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Event\TablePreSetDataEvent;
use EMC\TableBundle\Event\TablePostSetDataEvent;
use EMC\TableBundle\Column\ColumnFactoryInterface;

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

    public function getData() {
        return $this->data;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function handleRequest(Request $request) {
        $this->options['_query']['limit'] = (int) $request->get('_limit', $this->options['limit']);
        $this->options['_query']['page'] = (int) $request->get('_page', 1);
        $this->options['_query']['sort'] = (int) $request->get('_sort', $this->options['default_sorts']);
        $this->options['_query']['filter'] = $request->get('_filter', '');
    }

    public function getTable() {
        $dataProvider = self::getDataProvider($this->options['data_provider']);

        $queryBuilder = $this->type->getQueryBuilder($this->entityManager, $this->options);

        $dataProvider->setQueryBuilder($queryBuilder);
        $dataProvider->setColumns($this->columns);

        $query = $this->options['_query'];
        
        $data = $dataProvider->getData($query['page'], $query['sort'], $query['limit'], $query['filter']);
        $total = 0;

        if ($query['limit'] > 0 && count($data) > 0) {
            if (count($data) === $query['limit'] || $query['page'] > 1) {
                $total = $dataProvider->getTotal($query['filter']);
            }
        }
        
        $event = new TablePreSetDataEvent($this->type, $this->data, $this->options);
        $this->eventDispatcher->dispatch(TablePreSetDataEvent::NAME, $event);

        $table = new Table($this->type, $this->columns, $data, $total, $this->options);

        $event = new TablePostSetDataEvent($table, $this->data, $this->options);
        $this->eventDispatcher->dispatch(TablePostSetDataEvent::NAME, $event);

        return $table;
    }

    /**
     * @param string $class
     * @return \EMC\TableBundle\Provider\DataProviderInterface
     * @throws \InvalidArgumentException
     */
    private static function getDataProvider($class) {

        $reflection = new \ReflectionClass($class);
        $provider = $reflection->newInstance(array());

        if (!$provider instanceof \EMC\TableBundle\Provider\DataProviderInterface) {
            throw new \InvalidArgumentException;
        }
        return $provider;
    }

    public function add($name, $type, array $options = array()) {
        $this->columns[] = $this->factory->create($name, $type, count($this->columns), $options)->getColumn();
        return $this;
    }
}

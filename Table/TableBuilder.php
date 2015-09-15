<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Column\ColumnInterface;

/**
 * Description of TableBuilder
 *
 * @author emc
 */
class TableBuilder implements TableBuilderInterface {

    /**
     * @var TableFactoryInterface
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
    
    /**
     *
     * @var string
     */
    private $caption;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $query;

    /**
     * @var string
     */
    private $uid;

    function __construct(TableFactoryInterface $factory, ObjectManager $entityManager, TableTypeInterface $type, array $data = null, array $options = array()) {
        $this->factory = $factory;
        $this->entityManager = $entityManager;
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;

        $this->query = array(
            'route' => $options['route'],
            'page' => 1,
            'sort' => $options['default_sorts'],
            'limit' => $options['limit'],
            'filter' => null
        );
    }

    public function getUid() {
        return $this->uid;
    }

    public function setUid($uid) {
        $this->uid = $uid;
    }
    
    public function getCaption() {
        return $this->caption;
    }

    public function setCaption($caption) {
        $this->caption = $caption;
    }

    public function getData() {
        return $this->data;
    }

    public function getOptions() {
        return $this->options;
    }

    public function addColumn(ColumnInterface $column) {
        $this->columns[] = $column;
        return $this;
    }

    public function handleRequest(Request $request) {
        $this->query['limit'] = (int) $request->get('_limit', $this->options['limit']);
        $this->query['page'] = (int) $request->get('_page', 1);
        $this->query['sort'] = (int) $request->get('_sort', $this->options['default_sorts']);
        $this->query['filter'] = $request->get('_filter', '');
    }

    public function getTable() {
        $dataProvider = self::getDataProvider($this->options['data_provider']);

        $queryBuilder = $this->type->getQueryBuilder($this->entityManager, $this->options);

        $dataProvider->setQueryBuilder($queryBuilder);
        $dataProvider->setColumns($this->columns);

        $data = $dataProvider->getData($this->query['page'], $this->query['sort'], $this->query['limit'], $this->query['filter']);
        $total = 0;

        if ($this->query['limit'] > 0) {
            $total = $dataProvider->getTotal($this->query['filter']);
        }

        $table = new Table($this->uid, $this->type->getName(), $this->caption, $this->columns, $data, $total, $this->query);

        $this->factory->store($this, $this->type);

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

}

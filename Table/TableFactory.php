<?php

namespace EMC\TableBundle\Table;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use EMC\TableBundle\Session\TableSessionInterface;
use EMC\TableBundle\Table\Column\ColumnFactoryInterface;
use EMC\TableBundle\Table\Type\TableTypeInterface;
/**
 * TableFactory
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableFactory implements TableFactoryInterface {

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var TableSessionInterface
     */
    private $tableSession;

    /**
     * @var ColumnFactoryInterface
     */
    private $columnFactory;

    function __construct(ObjectManager $entityManager, EventDispatcherInterface $eventDispatcher, TableSessionInterface $tableSession, ColumnFactoryInterface $columnFactory) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->tableSession = $tableSession;
        $this->columnFactory = $columnFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(TableTypeInterface $type, array $data = null, array $options = array(), array $params = array()) {

        $options = $this->resolveOptions($type, $data, $options, $params);

        if ($options['subtable'] instanceof TableTypeInterface) {
            $subtable = $this->create($options['subtable'], null, $options['subtable_options'])->create();
            $options['_subtid'] = $subtable->getOption('_tid');
        }

        $builder = new TableBuilder($this->entityManager, $this->eventDispatcher, $this->columnFactory, $type, $data, $options);

        $type->buildTable($builder, $builder->getOptions());

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function load($class, array $data = null, array $options = array(), array $params = array()) {
        $type = $this->newInstance($class);
        return $this->create($type, $data, $options, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function restore($tableId, array $params = array()) {
        $config = $this->tableSession->restore($tableId);
        return $this->load($config['class'], $config['data'], $config['options'], $params);
    }

    /**
     * This method create a unique identifier for the table type and options.
     * @param \EMC\TableBundle\Table\Type\TableTypeInterface $type
     * @param array $options
     * @return string
     */
    private function generateTableId(TableTypeInterface $type, array $options) {
        return sha1(get_class($type) . $type->getName() . http_build_query($options));
    }

    /**
     * Create an instance of $class
     * @param string $class
     * @return \EMC\TableBundle\Table\Type\TableTypeInterface
     * @throws \InvalidArgumentException
     */
    private function newInstance($class) {
        assert(is_string($class));

        $reflection = new \ReflectionClass($class);
        $type = $reflection->newInstance();

        if (!$type instanceof TableTypeInterface) {
            throw new \InvalidArgumentException;
        }

        return $type;
    }

    /**
     * Resolve table type options
     * @param \EMC\TableBundle\Table\Type\TableTypeInterface $type
     * @param array $data
     * @param array $options
     * @param array $params
     * @return array
     */
    private function resolveOptions(TableTypeInterface $type, array $data = null, array $options = array(), array $params = array()) {
        if (null !== $data && !array_key_exists('data', $options)) {
            $options['data'] = $data;
        }

        $_options = $options;
        if (count($params) > 0) {
            $options['params'] = $params;
        }

        $resolver = $type->getOptionsResolver();
        $type->setDefaultOptions($resolver);

        $options = $resolver->resolve($options);

        $options['_tid'] = $this->generateTableId($type, $_options);
        $options['_passed_options'] = $_options;
        $options['_query'] = array(
            'page' => 1,
            'sort' => $options['default_sorts'],
            'limit' => $options['limit'],
            'filter' => null
        );

        return $options;
    }

}

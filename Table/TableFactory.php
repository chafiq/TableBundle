<?php

namespace EMC\TableBundle\Table;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use EMC\TableBundle\Session\TableSession;
use EMC\TableBundle\Column\ColumnFactoryInterface;

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
     * @var TableSession
     */
    private $tableSession;
    
    /**
     * @var ColumnFactoryInterface
     */
    private $columnFactory;
    
    function __construct(ObjectManager $entityManager, EventDispatcherInterface $eventDispatcher, TableSession $tableSession, ColumnFactoryInterface $columnFactory) {
        $this->entityManager    = $entityManager;
        $this->eventDispatcher  = $eventDispatcher;
        $this->tableSession     = $tableSession;
        $this->columnFactory    = $columnFactory;
    }
    
    public function create(TableTypeInterface $type, $data = null, array $options = array(), array $params=array()) {
        if (null !== $data && !array_key_exists('data', $options)) {
            $options['data'] = $data;
        }
        
        $options['params'] = $params;

        $resolver = new OptionsResolver();
        $type->setDefaultOptions($resolver);
        
        $_options = $options;
        $options = $resolver->resolve($options);
        
        $options['_tid'] = self::hash($type, $_options);
        
        if ( $options['subtable'] instanceof TableTypeInterface ) {
            $subtable = $this->create($options['subtable'], $data, $options['subtable_options'])->create();
            $options['_subtid'] = $subtable->getOption('_tid');
        }
        
        $builder = new TableBuilder($this->entityManager, $this->eventDispatcher, $this->columnFactory, $type, $data, $options);
        
        $type->buildTable($builder, $builder->getOptions());
        
        return $builder;
    }
    
    public function load($class, $data = null, array $options = array(), array $params=array()) {
        
        assert(is_string($class));
        
        $reflection = new \ReflectionClass($class);
        $type = $reflection->newInstance();
        
        if ( !$type instanceof TableTypeInterface ) {
            throw new \InvalidArgumentException;
        }
        return $this->create($type, $data, $options, $params);
    }
    
    public function restore($tableId, array $params=array()) {
        $config = $this->tableSession->restore($tableId);
        return $this->load($config['class'], $config['data'], $config['options'], $params);
    }
    
    private static function hash(TableTypeInterface $type, array $options) {
        return sha1(get_class($type) . $type->getName() . http_build_query($options));
    }
}
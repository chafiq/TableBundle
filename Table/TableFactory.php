<?php

namespace EMC\TableBundle\Table;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use EMC\TableBundle\Session\TableSession;

/**
 * Description of TableFactory
 *
 * @author emc
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
    
    function __construct(ObjectManager $entityManager, EventDispatcherInterface $eventDispatcher, TableSession $tableSession) {
        $this->entityManager    = $entityManager;
        $this->eventDispatcher  = $eventDispatcher;
        $this->tableSession     = $tableSession;
    }
    
    public function create(TableTypeInterface $type, $data = null, array $options = array()) {
        if (null !== $data && !array_key_exists('data', $options)) {
            $options['data'] = $data;
        }

        $resolver = new OptionsResolver();
        $type->setDefaultOptions($resolver);
        
        $options = $resolver->resolve($options);
        
        $builder = new TableBuilder($this->entityManager, $this->eventDispatcher, $type, self::hash($type, $options), $data, $options);
        
        $type->buildTable($builder, $builder->getOptions());

        return $builder;
    }
    
    public function load($class, $data = null, array $options = array()) {
        
        assert(is_string($class));
        
        $reflection = new \ReflectionClass($class);
        $type = $reflection->newInstance();
        
        if ( !$type instanceof TableTypeInterface ) {
            throw new \InvalidArgumentException;
        }
        return $this->create($type, $data, $options);
    }
    
    public function restore($tableId) {
        $config = $this->tableSession->restore($tableId);
        return $this->load($config['class'], $config['data'], $config['options']);
    }
    
    private static function hash(TableTypeInterface $type, array $options) {
        return sha1(get_class($type) . $type->getName() . http_build_query($options));
    }
}
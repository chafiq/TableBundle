<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var SessionInterface
     */
    private $session;
    
    function __construct(ObjectManager $entityManager, SessionInterface $session) {
        $this->session      = $session;
        $this->entityManager= $entityManager;
    }
    
    public function create(TableTypeInterface $type, $data = null, array $options = array()) {
        if (null !== $data && !array_key_exists('data', $options)) {
            $options['data'] = $data;
        }

        $resolver = new OptionsResolver();
        $type->setDefaultOptions($resolver);
        
        $options = $resolver->resolve($options);
        
        $builder = new TableBuilder($this, $this->entityManager, $type, $data, $options);
        
        $builder->setUid(self::hash($type, $options));
        
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
    
    public function restore($uid) {
        if ( !$this->session->has($uid) ) {
            throw new \InvalidArgumentException;
        }
        
        $config = $this->session->get($uid);
        
        return $this->load($config['class'], $config['data'], $config['options']);
    }
    
    public function store(TableBuilderInterface $builder, TableTypeInterface $type) {
        $this->session->set($builder->getUid(), array(
            'class'     => get_class($type),
            'data'      => $builder->getData(),
            'options'   => $builder->getOptions()
        ));
    }
    
    private static function hash(TableTypeInterface $type, array $options) {
        return sha1(get_class($type) . $type->getName() . http_build_query($options));
    }
}
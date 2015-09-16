<?php

namespace EMC\TableBundle\Table;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * TableType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class TableType implements TableTypeInterface {
    
    abstract public function buildTable(TableBuilderInterface $builder, array $options);

    abstract public function getQueryBuilder(ObjectManager $entityManager, array $options);

    abstract public function getName();

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $resolver->setDefaults(array(
            'name' => $this->getName(),
            'route'=> '_table',
            'data' => null,
            'data_provider' => 'EMC\TableBundle\Provider\DataProvider',
            'default_sorts' => array(),
            'limit' => 10,
            'selector' => false
        ));
        
        $resolver->setAllowedTypes(array(
            'name' => 'string',
            'route' => 'string',
            'data' => array('null', 'array'),
            'data_provider' => array('null', 'string'),
            'default_sorts' => 'array',
            'limit' => 'int',
            'selector' => 'bool'
        ));
    }

}

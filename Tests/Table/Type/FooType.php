<?php

namespace EMC\TableBundle\Tests\Table\Type;

use EMC\TableBundle\Table\Type\TableType;

/**
 * FooType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class FooType extends TableType {
    
    /**
     *
     * @var \Doctrine\ORM\QueryBuilder Mock
     */
    private $queryBuilder;
    
    function __construct(\Doctrine\ORM\QueryBuilder $queryBuilder=null) {
        $this->queryBuilder = $queryBuilder;
    }
    
    public function getId() {
        return '03bc791ec92a4d00683586852cd3bd75990883ed';
    }
    
    public function getQueryBuilder(\Doctrine\Common\Persistence\ObjectManager $entityManager = null, array $params) {
        return $this->queryBuilder;
    }
    
    public function getName() {
        return 'foo';
    }
}

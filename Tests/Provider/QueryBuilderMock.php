<?php
namespace EMC\TableBundle\Tests\Provider;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;

class QueryBuilderMock extends QueryBuilder {

    protected $expr;
    protected $paramReflect;
    
    public function __construct() {
        $this->paramReflect = new \ReflectionProperty('Doctrine\ORM\QueryBuilder', 'parameters'); 
        $this->paramReflect->setAccessible(true);
        $this->paramReflect->setValue($this, new ArrayCollection());
    }

    /*
     * @return Query\Expr
     */
    public function expr() {
        return $this->expr;
    }

    public function getEntityManager() {
        return null;
    }

    public function getQuery() {
        return new QueryMock(
            clone $this->paramReflect->getValue($this),
            $this->getDQL(),
            $this->getFirstResult(),
            $this->getFirstResult()
        );
    }
    
}
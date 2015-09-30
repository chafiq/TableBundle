<?php

namespace EMC\TableBundle\Provider;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface QueryConfigInterface {
    
    /**
     * @return array Query builder select
     */
    public function getSelect();

    /**
     * @return array Query builder orderBy
     */
    public function getOrderBy();

    /**
     * @return array Max rows per page
     */
    public function getLimit();

    /**
     * @return array Actual page number
     */
    public function getPage();

    /**
     * @return \Doctrine\ORM\Query\Expr\Orx Where clause constraints
     */
    public function getConstraints();

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @return boolean Query config is valid or not
     */
    public function isValid();
    
    /**
     * @param array $select
     * @return QueryConfigInterface
     */
    public function setSelect(array $select);

    /**
     * @param array $orderBy
     * @return QueryConfigInterface
     */
    public function setOrderBy(array $orderBy);

    /**
     * @param int $limit
     * @return QueryConfigInterface
     */
    public function setLimit($limit);

    /**
     * @param int $page
     * @return QueryConfigInterface
     */
    public function setPage($page);

    /**
     * 
     * @param \Doctrine\ORM\Query\Expr\Orx $constraints
     * @return QueryConfigInterface
     */
    public function setConstraints(\Doctrine\ORM\Query\Expr\Orx $constraints);

    /**
     * 
     * @param \Doctrine\ORM\Query\Expr\Andx $constraint
     * @return QueryConfigInterface
     */
    public function addConstraint(\Doctrine\ORM\Query\Expr\Andx $constraint);

    /**
     * 
     * @param array $parameters
     * @return QueryConfigInterface
     */
    public function setParameters(array $parameters);

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @return QueryConfigInterface
     */
    public function addParameter($name, $value);
    
    /**
     * 
     * @param boolean $isValid
     */
    public function setValid($isValid);
}

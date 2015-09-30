<?php

namespace EMC\TableBundle\Provider;

/**
 * QueryConfig
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class QueryConfig implements QueryConfigInterface {

    /**
     * @var array
     */
    private $select;

    /**
     * @var array
     */
    private $orderBy;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $page;

    /**
     * @var \Doctrine\ORM\Query\Expr\Orx
     */
    private $constraints;

    /**
     * @var array
     */
    private $parameters;

    /**
     *
     * @var boolean
     */
    private $isValid;

    function __construct() {
        $this->constraints = new \Doctrine\ORM\Query\Expr\Orx();
        $this->parameters = array();
        $this->isValid = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelect() {
        return $this->select;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderBy() {
        return $this->orderBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter() {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints() {
        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid() {
        return $this->isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelect(array $select) {
        $this->select = $select;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderBy(array $orderBy) {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage($page) {
        $this->page = $page;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConstraints(\Doctrine\ORM\Query\Expr\Orx $constraints) {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraint(\Doctrine\ORM\Query\Expr\Andx $constraint) {
        $this->constraints->add($constraint);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addParameter($name, $value) {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValid($isValid) {
        $this->isValid = $isValid;
    }

}

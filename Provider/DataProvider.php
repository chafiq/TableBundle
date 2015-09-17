<?php

namespace EMC\TableBundle\Provider;

use Doctrine\ORM\QueryBuilder;
use EMC\TableBundle\Column\ColumnInterface;

/**
 * DataProvider
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class DataProvider implements DataProviderInterface {

    /**
     * @var QueryBuilder 
     */
    private $queryBuilder;

    /**
     * @var array
     */
    private $columns;

    function __construct() {
        $this->queryBuilder = null;
        $this->columns = array();
        $this->data = array();
        $this->total = 0;
    }

    public function getData($page, $sort, $limit, $filter) {

        assert($limit >= 0);
        assert($page > 0);

        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder->resetDQLPart('select');

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit)
                    ->setFirstResult(($page - 1) * $limit);
        }

        $this->addFilter($queryBuilder, $filter);

        $indexes = array();
        foreach ($this->columns as $i => $column) {
            $options = $column->getOptions();
            if (count($options['params']) > 0) {
                foreach ($options['params'] as $j => $param) {
                    $indexes['col' . $i] = array($i, $j);
                    $queryBuilder->addSelect($param . ' as col' . $i . $j);

                    if ($sort !== 0 && $i === abs($sort) - 1) {
                        $queryBuilder->orderBy($param, $sort > 0 ? 'ASC' : 'DESC');
                    }
                }
            }
        }

        if (count($indexes) === 0) {
            return array();
        }
        $rows = $queryBuilder->getQuery()->getArrayResult();

        foreach ($rows as &$data) {
            $_data = array();

            foreach ($this->columns as $i => $column) {
                $options = $column->getOptions();
                if (count($options['params']) > 0) {
                    $__data = array();
                    foreach ($options['params'] as $j => $param) {
                        $__data[$param] = $data['col' . $i . $j];
                    }
                    $_data = array_merge($_data, $__data);
                }
            }

            $data = $_data;
        }
        unset($data);

        return $rows;
    }

    public function getTotal($filter) {

        $queryBuilder = clone $this->queryBuilder;

        $queryBuilder->resetDQLPart('select')
                ->resetDQLPart('orderBy')
                ->setMaxResults(1)
                ->setFirstResult(0);

        $this->addFilter($queryBuilder, $filter);

        return (int) $queryBuilder
                        ->select('count(distinct ' . $queryBuilder->getRootAlias() . '.id)')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    public function getAll() {
        return $this->getData(1, 0, 0);
    }

    public function setColumns(array $columns) {
        $this->columns = $columns;
        return $this;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder) {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    private function addFilter(QueryBuilder $queryBuilder, $filter) {
        if (!is_string($filter) || strlen($filter) === 0) {
            return;
        }

        if (strlen($filter) < 3) {
            throw new \InvalidArgumentException;
        }

        $orX = $queryBuilder->expr()->orX();

        $hasFilter = false;
        foreach ($this->columns as $column) {
            $options = $column->getOptions();
            if ($options['allow_filter'] && count($options['params']) > 0) {
                foreach ($options['params'] as $param) {
                    $orX->add('LOWER(' . $param . ') LIKE :filter');
                    $hasFilter = true;
                }
            }
        }

        if ($hasFilter) {
            $queryBuilder->andWhere($orX)
                    ->setParameter('filter', '%' . strtolower($filter) . '%');
        }
    }

}

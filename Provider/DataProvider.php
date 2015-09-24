<?php

namespace EMC\TableBundle\Provider;

use Doctrine\ORM\QueryBuilder;

/**
 * DataProvider
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class DataProvider implements DataProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function find(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        /* Add where clauses if there is any query search filter */
        $this->addConstraints($queryBuilder, $queryConfig);

        $rows = $this->getRows($queryBuilder, $queryConfig);
        $count = 0;
        if ($queryConfig->getLimit() > 0 && count($rows) > 0) {
            if (count($rows) === $queryConfig->getLimit() || $queryConfig->getPage() > 1) {
                $count = $this->getCount($queryBuilder, $queryConfig);
            }
        }

        return new QueryResult($rows, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        $queryConfig->setLimit(0);
        $queryConfig->setPage(1);

        return $this->find($queryBuilder, $queryConfig);
    }

    /**
     * Return rows according to the $queryConfig
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return array
     */
    private function getRows(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        $columns = array();

        $rows = $this->getQueryRows($queryBuilder, $queryConfig, $columns)->getArrayResult();

        return $this->resolveRowsKeys($rows, $columns);
    }

    /**
     * Return total row count
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return int
     */
    private function getCount(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        return (int) $this->getQueryCount($queryBuilder, $queryConfig)->getSingleScalarResult();
    }

    /**
     * Build and returns Query for select rows
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @param array $columns
     * @return \Doctrine\ORM\Query
     */
    private function getQueryRows(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig, array &$columns) {

        $queryBuilder->resetDQLPart('select');

        $limit = $queryConfig->getLimit();
        $page = $queryConfig->getPage();
        $select = $queryConfig->getSelect();
        $orderBy = $queryConfig->getOrderBy();

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit)
                    ->setFirstResult(($page - 1) * $limit);
        }

        $columns = array_map(function($i) {
            return 'col' . $i;
        }, array_flip($select));

        foreach ($columns as $column => $name) {
            $queryBuilder->addSelect($column . ' AS ' . $name);
        }

        if (count($orderBy) === 0) {
            $queryBuilder->orderBy($queryBuilder->getRootAlias() . '.id', 'ASC');
        } else {
            foreach ($orderBy as $column => $isAsc) {
                $queryBuilder->orderBy($column, $isAsc ? 'ASC' : 'DESC');
            }
        }

        return $queryBuilder->getQuery();
    }

    /**
     * Build and returns Query for count
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return \Doctrine\ORM\Query
     */
    private function getQueryCount(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        return $queryBuilder->resetDQLPart('select')
                        ->resetDQLPart('orderBy')
                        ->select('count(distinct ' . $queryBuilder->getRootAlias() . '.id)')
                        ->setMaxResults(1)
                        ->setFirstResult(0)
                        ->getQuery();
    }

    /**
     * Add filters constraints in the where clause on each allowed filter column
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @throws \InvalidArgumentException
     */
    private function addConstraints(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        $query = $queryConfig->getQuery();
        $columns = $queryConfig->getFilters();

        if (count($columns) === 0 || strlen($query) === 0) {
            return $queryBuilder;
        }

        if (strlen($query) < 3) {
            throw new \InvalidArgumentException;
        }

        $clause = implode(' OR ', array_map(function($col) {return 'LOWER(' . $col . ') LIKE :query';}, $columns));
        $queryBuilder->andWhere($clause);
        $queryBuilder->setParameter('query', '%' . strtolower($query) . '%');
        
        return $queryBuilder;
    }

    /**
     * Remap rows indexes<br/>
     * Examples :<br/>
     *  [col0] -> [t.id]<br/>
     *  [col1] -> [a.name]<br/>
     * @param array $rows
     * @param array $columns
     * @return array
     */
    private function resolveRowsKeys(array $rows, array $columns) {
        $keys = array_flip($columns);
        
        foreach ($rows as &$row) {
            $row = array_combine($keys, $row);
        }
        unset($row);
        
        return $rows;
    }

}

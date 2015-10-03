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
        if ($queryConfig->getConstraints()->count() > 0) {
            $queryBuilder->andWhere($queryConfig->getConstraints());
        }

        if (count($queryConfig->getParameters()) > 0) {
            foreach ($queryConfig->getParameters() as $key => $value) {
                $queryBuilder->setParameter($key, $value);
            }
        }

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
     * Return rows according to the $queryConfig
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return array
     */
    private function getRows(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        $mapping = array();

        $rows = $this->getQueryRows($queryBuilder, $queryConfig, $mapping)->getArrayResult();

        return $this->resolveRowsKeys($rows, $mapping);
    }

    /**
     * Return total row count
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @return int
     */
    private function getCount(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        return $this->getQueryCount($queryBuilder, $queryConfig)->getSingleScalarResult();
    }

    /**
     * Build and returns Query for select rows
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \EMC\TableBundle\Provider\QueryConfigInterface $queryConfig
     * @param array $mapping
     * @return \Doctrine\ORM\Query
     */
    private function getQueryRows(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig, array &$mapping) {
        $queryBuilder->resetDQLPart('select');

        $limit = $queryConfig->getLimit();
        $page = $queryConfig->getPage();
        $select = $queryConfig->getSelect();
        $orderBy = $queryConfig->getOrderBy();

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit)
                    ->setFirstResult(($page - 1) * $limit);
        }

        $mapping = array_map(function($i) {
            return 'col' . $i;
        }, array_flip($select));

        foreach ($mapping as $column => $name) {
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

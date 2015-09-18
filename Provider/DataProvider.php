<?php

namespace EMC\TableBundle\Provider;

use Doctrine\ORM\QueryBuilder;

/**
 * DataProvider
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class DataProvider implements DataProviderInterface {

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

    public function findAll(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        $queryConfig->setLimit(0);
        $queryConfig->setPage(1);

        return $this->getData($queryBuilder, $queryConfig);
    }
    
    private function getRows(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {

        $queryBuilder->resetDQLPart('select');

        $limit = $queryConfig->getLimit();
        $page = $queryConfig->getPage();
        $select = $queryConfig->getSelect();
        $orderBy = $queryConfig->getOrderBy();

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit)
                    ->setFirstResult(($page - 1) * $limit);
        }

        $columns = array_map(function($i){return 'col' . $i;}, array_flip($select));
        foreach ($columns as $column => $name) {
            $queryBuilder->addSelect($column . ' AS ' . $name);
        }

        foreach ($orderBy as $column => $isAsc) {
            $queryBuilder->orderBy($column, $isAsc ? 'ASC' : 'DESC');
        }

        $rows = $queryBuilder->getQuery()->getArrayResult();
        $keys = array_flip($columns);
        foreach( $rows as &$row ) {
            $row = array_combine($keys, $row);
        }
        unset($row);
        
        return $rows;
    }

    private function getCount(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        $queryBuilder->resetDQLPart('select')
                    ->resetDQLPart('orderBy')
                    ->setMaxResults(1)
                    ->setFirstResult(0);

        return (int) $queryBuilder
                        ->select('count(distinct ' . $queryBuilder->getRootAlias() . '.id)')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    private function addConstraints(QueryBuilder $queryBuilder, QueryConfigInterface $queryConfig) {
        $query = $queryConfig->getQuery();
        $columns = $queryConfig->getFilters();

        if (count($columns) === 0 || strlen($query) === 0) {
            return;
        }

        if (strlen($query) < 3) {
            throw new \InvalidArgumentException;
        }

        $clause = implode(' OR ', array_map(function($col) {
                    return 'LOWER(' . $col . ') LIKE :query';
                }, $columns));
        $queryBuilder->andWhere($clause)
                ->setParameter('query', '%' . strtolower($query) . '%');
    }

}

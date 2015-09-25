<?php

namespace EMC\TableBundle\Tests\Table\Type;

use EMC\TableBundle\Table\Type\TableType;
use EMC\TableBundle\Tests\Provider\QueryBuilderMock;

/**
 * MockType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class MockType extends TableType {

    public function buildTable(\EMC\TableBundle\Table\TableBuilderInterface $builder, array $options) {
        parent::buildTable($builder, $options);
        $builder->add('id', 'text', array(
                    'params' => array('id')
                ))
                ->add('name', 'text', array(
                    'params' => array('name')
        ));
    }

    public function getQueryBuilder(\Doctrine\Common\Persistence\ObjectManager $entityManager = null, array $params) {
        $queryBuilder = new QueryBuilderMock();
        $queryBuilder->from('Table', 't');
        return $queryBuilder;
    }

    public function getName() {
        return 'mock';
    }

}

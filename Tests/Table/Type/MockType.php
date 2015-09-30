<?php

namespace EMC\TableBundle\Tests\Table\Type;

use Doctrine\Common\Persistence\ObjectManager;
use EMC\TableBundle\Table\Type\TableType;
use EMC\TableBundle\Tests\Provider\QueryBuilderMock;
use EMC\TableBundle\Table\TableBuilderInterface;

/**
 * MockType
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class MockType extends TableType {

    public function buildTable(TableBuilderInterface $builder, array $options) {
        parent::buildTable($builder, $options);
        $builder->add('id', 'text', array(
                    'title' => 'ID',
                    'params' => array('id')
                ))
                ->add('name', 'text', array(
                    'title' => 'Name',
                    'params' => array('name')
        ));
    }

    public function getQueryBuilder(ObjectManager $entityManager = null, array $params) {
        $queryBuilder = new QueryBuilderMock();
        $queryBuilder->from('Table', 't');
        return $queryBuilder;
    }

    public function getName() {
        return 'mock';
    }

}

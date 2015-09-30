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

    /**
     * @var add
     */
    private $add;

    function __construct(\Doctrine\ORM\QueryBuilder $queryBuilder = null, $add = false) {
        $this->queryBuilder = $queryBuilder;
        $this->add = $add;
    }

    public function buildTable(\EMC\TableBundle\Table\TableBuilderInterface $builder, array $options) {
        parent::buildTable($builder, $options);

        if ($this->add) {
            $builder->add('id', 'text', array(
                        'params' => array('id')
                    ))
                    ->add('name', 'text', array(
                        'params' => array('name')
                    ));
        }
    }

    public function getId() {
        return '03bc791ec92a4d00683586852cd3bd75990883ed';
    }

    public function getQueryBuilder(\Doctrine\Common\Persistence\ObjectManager $entityManager, array $params) {
        return $this->queryBuilder;
    }

    public function getName() {
        return 'foo';
    }

}

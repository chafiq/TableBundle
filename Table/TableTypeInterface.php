<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface TableTypeInterface {
    public function buildTable(TableBuilderInterface $builder, array $options);
    public function buildView(TableView $view, TableInterface $table, array $options = array());
    public function setDefaultOptions(OptionsResolverInterface $resolver);
    public function getQueryBuilder(ObjectManager $entityManager, array $options);
    public function getName();
}

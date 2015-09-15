<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 *
 * @author emc
 */
interface TableTypeInterface {
    public function buildTable(TableBuilderInterface $builder, array $options);
    public function setDefaultOptions(OptionsResolverInterface $resolver);
    public function getQueryBuilder(ObjectManager $entityManager, array $options);
    public function getName();
}

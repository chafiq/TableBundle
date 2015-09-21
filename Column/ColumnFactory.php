<?php

namespace EMC\TableBundle\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;
use EMC\TableBundle\Table\TableRegistryInterface;

/**
 * ColumnFactory Service
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnFactory implements ColumnFactoryInterface {

    /**
     * @var TableRegistryInterface
     */
    private $registry;

    function __construct(TableRegistryInterface $registry) {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $type, $idx, array $options = array()) {

        $type = $this->resolve($type);

        $resolver = new OptionsResolver();
        $type->setDefaultOptions($resolver);

        $options = $resolver->resolve($options);
        $options['name'] = $name;
        $options['idx'] = $idx;

        $builder = new ColumnBuilder($type, $options);

        $type->buildColumn($builder, $options);

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    private function resolve($type) {
        if ($type instanceof ColumnTypeInterface) {
            return $type;
        } else if (is_string($type)) {
            return $this->registry->getType($type);
        }
        throw new \InvalidArgumentException('Column type "' . $type . '" unknown');
    }

}

<?php

namespace EMC\TableBundle\Table\Column;

use EMC\TableBundle\Table\Column\ColumnRegistryInterface;

use EMC\TableBundle\Table\Column\Type\ColumnTypeInterface;

/**
 * ColumnFactory Service
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnFactory implements ColumnFactoryInterface {

    /**
     * @var ColumnRegistryInterface
     */
    private $registry;

    function __construct(ColumnRegistryInterface $registry) {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $type, array $options, array $defaultOptions) {
        
        $type = $this->resolve($type);

        $_options = $options;
        
        $resolver = $type->getOptionsResolver();
        $type->setDefaultOptions($resolver, $defaultOptions);

        $options['name'] = $name;
        $options = $resolver->resolve($options);
        $options['_passed_options'] = $_options;

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
        throw new \InvalidArgumentException('$type ColumnTypeInterface|string expected, "' . gettype($type) . '" found');
    }

}

<?php

namespace EMC\TableBundle\Table\Column;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * ColumnRegistry
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnRegistry extends ContainerAware implements ColumnRegistryInterface {

    /**
     * @var array
     */
    private $types;

    function __construct(array $types) {
        $this->types = $types;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getType($name) {
        if (!isset($this->types[$name])) {
            throw new \InvalidArgumentException(sprintf('The field type "%s" is not registered with the service container.', $name));
        }

        $type = $this->container->get($this->types[$name]);

        if ($type->getName() !== $name) {
            throw new \InvalidArgumentException(
            sprintf('The type name specified for the service "%s" does not match the actual name. Expected "%s", given "%s"', $this->types[$name], $name, $type->getName()
            ));
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($name) {
        return isset($this->types[$name]);
    }

}

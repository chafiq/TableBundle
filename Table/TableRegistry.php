<?php

namespace EMC\TableBundle\Table;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ColumnRegistry
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableRegistry implements TableRegistryInterface {

    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var array
     */
    private $types;

    function __construct(ContainerInterface $container, $types) {
        $this->container = $container;
        $this->types = $types;
    }
    
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

    public function hasType($name) {
        return isset($this->types[$name]);
    }

}

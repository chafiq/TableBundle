<?php

namespace EMC\TableBundle\Table\Export;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * ExportRegistry
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ExportRegistry extends ContainerAware implements ExportRegistryInterface {

    /**
     * @var array
     */
    private $exports;

    function __construct(array $exports) {
        $this->exports = $exports;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($name) {
        if (!isset($this->exports[$name])) {
            throw new \InvalidArgumentException(sprintf('The field export "%s" is not defined in config.yml (default) and not registered as service export extension. @see Documentation "Export" section', $name));
        }

        $export = $this->container->get($this->exports[$name]);

        if ($export->getName() !== $name) {
            throw new \InvalidArgumentException(
            sprintf('The export name specified for the service "%s" does not match the actual name. Expected "%s", given "%s"', $this->exports[$name], $name, $export->getName()
            ));
        }

        return $export;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name) {
        return isset($this->exports[$name]);
    }

}

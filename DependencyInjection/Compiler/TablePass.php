<?php

namespace EMC\TableBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * TablePass
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TablePass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        if (!$container->hasDefinition('table.extension')) {
            return;
        }

        $this->load($container, 'table.column.registry', 'column.type');
        $this->load($container, 'table.export.registry', 'export.extension');
    }
    
    private function load(ContainerBuilder $container, $service, $tag) {
        if (!$container->hasDefinition('table.extension')) {
            return;
        }

        $definition = $container->getDefinition($service);

        // Builds an array with service IDs as keys and tag aliases as values
        $services = array();

        foreach ($container->findTaggedServiceIds($tag) as $id => $config) {
            $alias = isset($config[0]['alias']) ? $config[0]['alias'] : $id;
            $services[$alias] = $id;
        }

        $definition->replaceArgument(1, $services);
    }

}

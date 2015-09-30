<?php

namespace EMC\TableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('emc_table');

        $rootNode
            ->children()
                ->scalarNode('template')
                    ->defaultValue('EMCTableBundle::template.html.twig')
                ->end()
                ->scalarNode('default_extensions')
                    ->defaultValue('EMCTableBundle::extensions.html.twig')
                ->end()
                ->arrayNode('extensions')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('export')
                    ->children()
                        ->arrayNode('csv')
                            ->children()
                                ->scalarNode('delimiter')
                                    ->defaultValue(',')
                                ->end()
                                ->scalarNode('enclosure')
                                    ->defaultValue('"')
                                ->end()
                                ->scalarNode('escape')
                                    ->defaultValue('\\')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('pdf')
                            ->children()
                                ->scalarNode('template')
                                    ->defaultValue('EMCTableBundle:Export:pdf.html.twig')
                                ->end()
                                ->scalarNode('bin')
                                    ->defaultValue('wkhtmltopdf')
                                ->end()
                                ->arrayNode('options')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode('timeout')
                                            ->defaultValue(60)
                                        ->end()
                                        ->enumNode('page-size')
                                            ->values(array('A3', 'A4', 'Letter'))
                                            ->defaultValue('A4')
                                        ->end()
                                        ->scalarNode('title')
                                            ->defaultValue('Export [caption]')
                                        ->end()
                                        ->scalarNode('filename')
                                            ->defaultValue('Export [caption] [now]')
                                        ->end()
                                        ->enumNode('orientation')
                                            ->values(array('Landscape', 'Portrait'))
                                            ->defaultValue('Portrait')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('xls')
                            ->children()
                                ->scalarNode('template')
                                    ->defaultValue('EMCTableBundle:Export:xls.xml.twig')
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('template')
                            ->defaultValue('EMCTableBundle:Export:template.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

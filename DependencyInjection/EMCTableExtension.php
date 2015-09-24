<?php

namespace EMC\TableBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EMCTableExtension extends Extension {

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('emc_table.template', isset($config['template']) ? $config['template'] : 'EMCTableBundle::template.html.twig');
        $extensions = array('EMCTableBundle::extensions.html.twig');
        if ( isset($config['extensions']) ) {
            $extensions = array_unique(array_merge($extensions, $config['extensions']));
        }
        $container->setParameter('emc_table.extensions',  $extensions);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
    
    private function loadTemplates($template, array $extensions) {
        
    }

}

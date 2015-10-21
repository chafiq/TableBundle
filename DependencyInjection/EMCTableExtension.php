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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        array_unshift($config['extensions'], $config['default_extensions']);
        $container->setParameter('emc_table.extensions', $config['extensions']);
        $container->setParameter('emc_table.template', $config['template']);
        $container->setParameter('emc_table.default_options', $config['options']);
        $container->setParameter('emc_table.default_column_options', $config['column']['options']);

        $loader->load('table.services.yml');
        $loader->load('column.services.yml');
        $loader->load('profiler.services.yml');

        if (isset($config['export'])) {
            $loader->load('export.services.yml');
            $container->setParameter('emc_table.export.template', $config['export']['template']);
        }

        if (isset($config['export']['csv'])) {
            $container->setParameter('emc_table.export.csv.delimiter', $config['export']['csv']['delimiter']);
            $container->setParameter('emc_table.export.csv.enclosure', $config['export']['csv']['enclosure']);
            $container->setParameter('emc_table.export.csv.escape', $config['export']['csv']['escape']);
            $loader->load('csv.export.services.yml');
        }

        if (isset($config['export']['pdf'])) {
            $container->setParameter('emc_table.export.pdf.bin', $config['export']['pdf']['bin']);
            $container->setParameter('emc_table.export.pdf.template', $config['export']['pdf']['template']);
            $container->setParameter('emc_table.export.pdf.options', $config['export']['pdf']['options']);
            $loader->load('pdf.export.services.yml');
        }

        if (isset($config['export']['xls'])) {
            $container->setParameter('emc_table.export.xls.template', $config['export']['xls']['template']);
            $loader->load('xls.export.services.yml');
        }
    }

}

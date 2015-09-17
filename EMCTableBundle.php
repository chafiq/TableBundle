<?php

namespace EMC\TableBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use EMC\TableBundle\DependencyInjection\Compiler\TablePass;

class EMCTableBundle extends Bundle {
    public function build(ContainerBuilder $container) {
        parent::build($container);
        
        $container->addCompilerPass(new TablePass());
    }
}

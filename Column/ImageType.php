<?php

namespace EMC\TableBundle\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Image Column Type
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ImageType extends ColumnType {
    
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        
        $view['asset_url'] = $options['asset_url'];
        $view['alt'] = $options['alt'];
        $view['output'] = $options['output'] ? $options['output'] : null;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'asset_url'   => null,
            'alt'   => '',
            'output'=> ''
        ));
        
        $resolver->addAllowedTypes(array(
            'asset_url'       => 'string',
            'alt'       => 'string',
            'output'    => 'string'
        ));
    }
    
    public function getName() {
        return 'image';
    }
}
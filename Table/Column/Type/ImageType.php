<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Image Column Type
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ImageType extends ColumnType {
    
    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        
        $view['asset_url'] = $options['asset_url'];
        $view['alt'] = $options['alt'];
        $view['output'] = $options['output'];
    }
    
    /**
     * {@inheritdoc}
     * <ul>
     * <li><b>asset_url</b>     : string <i>Asset url of the image.</i></li>
     * <li><b>alt</b>   : string <i>Alternative text if image does not exists.</i></li>
     * <li><b>output</b>   : string <i>Asset output url.</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'asset_url'   => null,
            'alt'   => '',
            'output'=> ''
        ));
        
        $resolver->addAllowedTypes(array(
            'asset_url' => 'string',
            'alt'       => 'string',
            'output'    => 'string'
        ));
    }
    
    public function isExportable() {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'image';
    }
}
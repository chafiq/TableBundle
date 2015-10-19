<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Image Column Type
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ImageType extends AnchorType {
    
    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        
        $view['asset_url'] = self::toString('asset', $options['asset'], $data);
        $view['alt'] = $options['alt'];
    }
    
    /**
     * {@inheritdoc}
     * <br/>
     * <br/>
     * Available Options :
     * <ul>
     * <li><b>anchor_route</b>  : string|null <i>Anchor route, default null.</i></li>
     * <li><b>asset</b>         : string|callable <i>Asset url of the image.</i></li>
     * <li><b>alt</b>           : string <i>Alternative text if image does not exists.</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'asset'   => null,
            'alt'   => ''
        ));
        
        $resolver->addAllowedTypes(array(
            'anchor_route' => array('null', 'string'),
            'asset'     => array('string', 'callable'),
            'alt'       => 'string'
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'image';
    }
}

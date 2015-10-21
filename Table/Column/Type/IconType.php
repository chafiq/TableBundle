<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Icon Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class IconType extends AnchorType {
    
    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        
        $icon = $options['icon'];
        if (is_callable($options['icon']) ) {
            $icon = self::format($options['icon'], $data, array('callable'));
        }
        if ( !is_string($icon) ) {
            throw new \UnexpectedValueException('$icon must be a string');
        }
        
        $view['icon_class'] = sprintf('%s %s-%s', $options['icon_family'], $options['icon_family'], $icon);
    }
    
    /**
     * {@inheritdoc}
     * <br/>
     * <br/>
     * Available Options :
     * <ul>
     * <li><b>anchor_route</b>  : string|null <i>Anchor route, default null.</i></li>
     * <li><b>icon</b>          : string|callable <i>Icon name without prefix icon_family : fa-table => table</i></li>
     * <li><b>icon_family</b>   : string <i>Icon family (fa|icon|glyphicon|...). Default is defined bundle config.</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver, array $defaultOptions) {
        parent::setDefaultOptions($resolver, $defaultOptions);
        
        $resolver->setDefaults(array(
            'icon' => null,
            'icon_family' => $defaultOptions['icon_family']
        ));
        
        $resolver->addAllowedTypes(array(
            'anchor_route' => array('null', 'string'),
            'icon' => array('string', 'callable'),
            'icon_family' => 'string'
        ));
        
    }
    
    public function isExportable() {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'icon';
    }
}

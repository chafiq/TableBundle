<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Icon Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class IconType extends ColumnType {
    
    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        
        $view['icon'] = isset($options['icon']) && $options['icon'] ? $options['icon'] : $view['value'];
        $view['icon_family'] = $options['icon_family'];
    }
    
    /**
     * {@inheritdoc}
     * <ul>
     * <li><b>icon</b>          : string <i>Icon name without prefix icon_family : fa-table => table</i></li>
     * <li><b>icon_family</b>   : string <i>Icon family (fa|icon|glyphicon|...).</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'icon'          => '',
            'icon_family'   => 'fa'
        ));
        
        $resolver->addAllowedTypes(array(
            'icon'          => 'string',
            'icon_family'   => 'string'
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
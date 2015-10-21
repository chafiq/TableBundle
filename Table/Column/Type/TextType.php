<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Text Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TextType extends AnchorType {
    
    /**
     * {@inheritdoc}
     * <br/>
     * <br/>
     * Available Options :
     * <ul>
     * <li><b>anchor_route</b>  : string|null <i>Anchor route, default null.</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver, array $defaultOptions) {
        parent::setDefaultOptions($resolver, $defaultOptions);
        
        $resolver->setAllowedTypes(array(
            'anchor_route' => array('null', 'string')
        ));
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'text';
    }

}

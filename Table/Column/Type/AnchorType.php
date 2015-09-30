<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Anchor Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class AnchorType extends ColumnType {

    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        
        $view = array_merge($view, array(
            'route' => $options['route'],
            'params' => $this->resolveParams($options['params'], $options['static_params'], $data),
            'value' => isset($options['text']) ? $options['text'] : $view['value'],
            'title' => $options['desc'],
            'icon' => $options['icon']
        ));
        
    }
    
    /**
     * {@inheritdoc}
     * <ul>
     * <li><b>route</b>         : string <i>Route name. if the route requires arguments, be sur that they are present in 'params'</i></li>
     * <li><b>static_params</b> : string <i>Static params the add to the route params.</i></li>
     * <li><b>text</b>          : string|null <i>Anchor text. If null $view['value'] replace it.</i></li>
     * <li><b>attrs</b>         : array <i>Table Dom element attributes</i></li>
     * <li><b>icon</b>          : string|null <i>icon name.</i></li>
     * <li><b>desc</b>          : string|null <i>Anchor title</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);

        
        $resolver->setDefaults(array(
            'route'          => null,
            'static_params' => array(),
            'text'          => null,
            'icon'          => null,
            'desc'          => null
        ));
        
        $resolver->setAllowedTypes(array(
            'route'          => 'string',
            'static_params' => 'array',
            'text'          => array('null', 'string'),
            'icon'          => array('null', 'string'),
            'desc'          => array('null', 'string')
        ));
        
        $resolver->setNormalizers(array(
            'params'    => function(Options $options, $params) {
                assert(is_array($params));
                foreach ($params as $param => $_) {
                    assert(is_string($param));
                }
                return $params;
            }
        ));
        
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'anchor';
    }

    private function resolveParams(array $params, array $args, array $data) {
        foreach ($params as $key => &$param) {
            $param = $data[$key];
        }
        unset($param);

        if (!is_array($args) || count($args) === 0) {
            return $params;
        }

        return array_merge($params, $args);
    }
}

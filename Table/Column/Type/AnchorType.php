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

        if ($options['anchor_route'] === null) {
            return;
        }
        
        $view = array_merge($view, array(
            'route' => $options['anchor_route'],
            'params' => $this->resolveParams($options['params'], $options['anchor_params'], $options['anchor_args'], $data),
            'value' => $options['anchor_text'] ?: $view['value'],
            'title' => $options['anchor_title']
        ));
    }

    /**
     * {@inheritdoc}
     * <br/>
     * <br/>
     * Available Options :
     * <ul>
     * <li><b>anchor_route</b>  : string <i>Route name. if the route requires arguments, be sur that they are present in 'params'</i></li>
     * <li><b>anchor_params</b> : array|null <i>Route's params.</i></li>
     * <li><b>anchor_args</b>   : array <i>Route's static params.</i></li>
     * <li><b>anchor_text</b>   : string|null <i>Anchor text. If null $view['value'] replace it.</i></li>
     * <li><b>anchor_title</b>  : string|null <i>Anchor title</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'anchor_route' => null,
            'anchor_params' => array(),
            'anchor_args' => array(),
            'anchor_text' => '',
            'anchor_title' => ''
        ));

        $resolver->setAllowedTypes(array(
            'anchor_route' => 'string',
            'anchor_params' => array('null', 'array'),
            'anchor_args' => 'array',
            'anchor_text' => 'string',
            'anchor_title' => 'string'
        ));

        $resolver->setNormalizers(array(
            'anchor_params' => function(Options $options, $params) {
                if (!is_array($params) || is_null($options['anchor_route'])) {
                    return array();
                } else if (count($params) === 0) {
                    $params = array_keys($options['params']);
                    foreach ($params as $name ) {
                        if (!is_string($name)) {
                            throw new \UnexpectedValueException('Anchor params must be an associative array');
                        }
                    }
                } else  {
                    $diff = array_diff($params, array_keys($options['params']));
                    if (count($diff) > 0) {
                        throw new \UnexpectedValueException('anchor_params (' . implode(',', $diff) . ') must be defined in params');
                    }
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

    private function resolveParams(array $params, array $anchorParams, array $anchorArgs, array $data) {
        $params = array_intersect_key($params, array_flip($anchorParams));
        foreach ($params as $key => &$param) {
            $param = $data[$key];
        }
        unset($param);

        if (!is_array($anchorArgs) || count($anchorArgs) === 0) {
            return $params;
        }

        return array_merge($params, $anchorArgs);
    }

}

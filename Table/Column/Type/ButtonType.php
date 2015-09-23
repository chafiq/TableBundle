<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Button Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ButtonType extends IconType {

    /**
     * {@inheritdoc}
     * <ul>
     * <li><b>text</b>          : string|null <i>Button text. If null $view['value'] replace it.</i></li>
     * <li><b>desc</b>          : string|null <i>Button title</i></li>
     * </ul>
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        $view['text'] = isset($options['text']) ? $options['text'] : $view['value'];
        $view['title'] = $options['desc'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'text' => null,
            'desc' => null
        ));

        $resolver->setAllowedTypes(array(
            'text' => array('null', 'string'),
            'desc' => array('null', 'string')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'button';
    }

}

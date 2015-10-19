<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Date Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class DatetimeType extends ColumnType {

    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        $view['value'] = $view['value']->format($options['date_format']);
    }
    
    /**
     * {@inheritdoc}
     * <br/>
     * <br/>
     * Available Options :
     * <ul>
     * <li><b>date_format</b>          : string <i>Date format : "Y-m-d H:i:s"</i></li>
     * </ul>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'date_format' => 'd/m/Y H:i'
        ));

        $resolver->addAllowedTypes(array(
            'date_format' => 'string'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'datetime';
    }

}

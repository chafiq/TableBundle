<?php

namespace EMC\TableBundle\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $view['date_format'] = $options['date_format'];
    }
    
    /**
     * {@inheritdoc}
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

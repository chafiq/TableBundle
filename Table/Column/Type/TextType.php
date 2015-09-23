<?php

namespace EMC\TableBundle\Table\Column\Type;

/**
 * Text Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TextType extends ColumnType {
    
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'text';
    }

}

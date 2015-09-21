<?php

namespace EMC\TableBundle\Column;

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

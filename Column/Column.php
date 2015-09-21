<?php

namespace EMC\TableBundle\Column;

/**
 * Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class Column implements ColumnInterface {

    /**
     * Column's type
     * @var ColumnTypeInterface
     */
    private $type;

    /**
     * Column's options
     * 
     * @var array
     */
    private $options;

    function __construct(ColumnTypeInterface $type, array $options = array()) {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name) {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException;
        }

        return $this->options[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveAllowedParams($name) {
        $option = $this->getOption($name);
        if ((is_bool($option) && (!$option || count($this->options['params']) === 0)) || (is_array($option) && count($option) === 0)
        ) {
            return null;
        } elseif (is_bool($option)) {
            return $this->options['params'];
        }
        return $option;
    }

}

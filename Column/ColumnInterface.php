<?php

namespace EMC\TableBundle\Column;

/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ColumnInterface {
    public function getName();
    public function getParams();
    public function format(array $data);
    public function getExtension();
    public function getView($data);
}

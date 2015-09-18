<?php

namespace EMC\TableBundle\Provider;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface QueryResultInterface {
    public function getRows();
    public function getCount();
}

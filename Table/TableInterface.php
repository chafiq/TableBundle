<?php

namespace EMC\TableBundle\Table;

/**
 *
 * @author emc
 */
interface TableInterface {
    public function getId();
    public function getName();
    public function getCaption();
    public function getColumns();
    public function getData();
    public function getTotal();
    public function getQuery();
}

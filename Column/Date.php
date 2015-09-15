<?php

namespace EMC\TableBundle\Column;

/**
 * Description of Text
 *
 * @author emc
 */
class Date extends Text implements ColumnInterface {
    
    /**
     * @var string
     */
    private $dateFormat;
    
    public static $DEFAULT_DATE_FORMAT = 'd/m/Y H:i';

    public function __construct($name, array $params = array()) {
        parent::__construct($name, $params);
        $this->dateFormat = self::$DEFAULT_DATE_FORMAT;
    }
    
    public function getDateFormat() {
        return $this->dateFormat;
    }

    public function setDateFormat($dateFormat) {
        $this->dateFormat = $dateFormat;
    }

    public function format(array $data) {
        foreach( $data as &$_data ) {
            if ( $_data instanceof \DateTime ) {
                $_data = $_data->format($this->dateFormat);
            }
        }
        return parent::format($data);
    }
}

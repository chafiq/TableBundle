<?php

namespace EMC\TableBundle\Tests;

/**
 * AbstractUnitTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
abstract class AbstractUnitTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * @var array
     */
    protected $defaultOptions = array(
        'route' => '_table',
        'select_route' => '_table_select',
        'export_route' => '_table_export',
        'data_provider' => 'EMC\TableBundle\Provider\DataProvider',
        'limit' => 10,
        'rows_pad' => true
    );

    /**
     * @var array
     */
    protected $defaultColumnOptions = array(
        'icon_family' => 'fa',
        'date_format' => 'SHORT',
        'time_format' => 'SHORT',
    );
    
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $name 
     * @param mixed  $value 
     *
     * @return object.
     */
    protected function invokeSetter(&$object, $name, $value) {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        return $object;
    }

}

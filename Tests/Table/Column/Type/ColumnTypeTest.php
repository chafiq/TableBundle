<?php

namespace EMC\TableBundle\Tests\Table\Column\Type;

use EMC\TableBundle\Table\Column\Column;
use EMC\TableBundle\Tests\AbstractUnitTest;

/**
 * ColumnTypeTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ColumnTypeTest extends AbstractUnitTest {

    public function testBuildView() {

        $type = new BarType();
        $optionsResolver = $type->getOptionsResolver();
        $type->setDefaultOptions($optionsResolver, $this->defaultColumnOptions);
        $options = array(
            'name' => 'foo',
            'attrs' => array('a' => 1, 'b' => 2)
        );
        $resolvedOptions = $optionsResolver->resolve($options);

        $view = array();

        $column = new Column($type, $resolvedOptions);

        $type->buildView($view, $column, array('test'), $resolvedOptions);

        $expectedViewKeys = array(
            'name',
            'type',
            'attrs',
            'value',
        );

        foreach ($expectedViewKeys as $key) {
            $this->assertArrayHasKey($key, $view);
        }
        $this->assertArrayHasKey('class', $view['attrs']);
        $this->assertEquals( 'foo', $view['name'] );
        $this->assertEquals( $view['type'], $type->getName() );
        $this->assertEquals( $view['value'], 'test' );
        $this->assertEquals( trim('column-' . $type->getName() . ' column-foo'), $view['attrs']['class']);
        $this->assertArrayHasKey( 'a', $view['attrs'] );
        $this->assertArrayHasKey( 'b', $view['attrs'] );
    }

    public function testFormat() {
        $barType = new BarType();

        $start = new \DateTime();
        $end = new \DateTime();
        $end->add(new \DateInterval('P1M'));
        $fromTo = 'From ' . $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d');


        $tests = array(
            'test' => array(null, array('test')),
            '"test" - 123' => array('"%s" - %d', array('test', 123)),
            $fromTo => array(function(\DateTime $start, \DateTime $end) {
            return 'From ' . $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d');
        }, array($start, $end))
        );

        foreach ($tests as $extected => $data) {
            $this->assertEquals($extected, $this->invokeMethod($barType, 'format', $data));
        }
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetValueException() {
        $barType = new BarType();
        $this->invokeMethod($barType, 'format', array(null, array('test', 123)));
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}

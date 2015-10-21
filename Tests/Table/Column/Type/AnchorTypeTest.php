<?php

namespace EMC\TableBundle\Tests\Table\Column\Type;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Table\Column\Type\AnchorType;
use EMC\TableBundle\Table\Column\Column;

/**
 * AnchorTypeTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class AnchorTypeTest extends AbstractUnitTest {

    public function testBuildView() {

        $type = new AnchorType();
        $optionsResolver = $type->getOptionsResolver();
        $type->setDefaultOptions($optionsResolver, $this->defaultColumnOptions);
        $options = array(
            'name' => 'foo',
            'params'=> array('k' => 'i', 'l' => 'j'),
            'format'=> '%d:%d',
            'attrs' => array('a' => 1, 'b' => 2),
            'anchor_args' => array('x' => 'y', 'z' => 1),
            'anchor_route' => 'route',
        );
        $resolvedOptions = $optionsResolver->resolve($options);
        $view = array();

        $column = new Column($type, $resolvedOptions);

        $type->buildView($view, $column, array('k' => 1, 'l' => 2), $resolvedOptions);

        $expectedViewKeys = array(
            'name',
            'type',
            'attrs',
            'value',
            'route',
            'params',
            'title'
        );

        foreach ($expectedViewKeys as $key) {
            $this->assertArrayHasKey($key, $view);
        }
        $this->assertArrayHasKey('class', $view['attrs']);
        $this->assertEquals('foo', $view['name']);
        $this->assertEquals($view['type'], $type->getName());
        $this->assertEquals($view['value'], '1:2');
        $this->assertEquals($view['route'], $resolvedOptions['anchor_route']);
        $this->assertEquals($view['params'], array('k' => 1, 'l' => 2, 'x' => 'y', 'z' => 1));
        $this->assertEquals($view['title'], $resolvedOptions['anchor_title']);
        $this->assertEquals(trim('column-' . $type->getName() . ' column-foo'), $view['attrs']['class']);
        $this->assertArrayHasKey('a', $view['attrs']);
        $this->assertArrayHasKey('b', $view['attrs']);
        $this->assertEquals(array('x' => 'y', 'z' => 1, 'k' => 1, 'l' => 2), $view['params']);
        $this->assertArrayHasKey('b', $view['attrs']);
    }

}

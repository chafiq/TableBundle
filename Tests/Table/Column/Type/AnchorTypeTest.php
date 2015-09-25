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
        $type->setDefaultOptions($optionsResolver);
        $options = array(
            'name' => 'foo',
            'params'=> array('k' => 'i', 'l' => 'j'),
            'format'=> '%d:%d',
            'attrs' => array('a' => 1, 'b' => 2),
            'static_params' => array('x' => 'y', 'z' => 1),
            'route' => 'route',
            'icon' => 'icon',
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
            'allow_sort',
            'allow_filter',
            'route',
            'text',
            'title',
            'icon',
            'desc'
        );

        foreach ($expectedViewKeys as $key) {
            $this->assertArrayHasKey($key, $view);
        }
        $this->assertArrayHasKey('class', $view['attrs']);
        $this->assertEquals('foo', $view['name']);
        $this->assertEquals($view['type'], $type->getName());
        $this->assertEquals($view['value'], '1:2');
        $this->assertEquals($view['route'], $resolvedOptions['route']);
        $this->assertEquals($view['text'], '1:2');
        $this->assertEquals($view['title'], $resolvedOptions['title']);
        $this->assertEquals($view['icon'], $resolvedOptions['icon']);
        $this->assertEquals($view['desc'], $resolvedOptions['desc']);
        $this->assertEquals($view['allow_sort'], $resolvedOptions['allow_sort']);
        $this->assertEquals($view['allow_filter'], $resolvedOptions['allow_filter']);
        $this->assertEquals(trim('column-' . $type->getName() . ' column-foo'), $view['attrs']['class']);
        $this->assertArrayHasKey('a', $view['attrs']);
        $this->assertArrayHasKey('b', $view['attrs']);
        $this->assertEquals(array('x' => 'y', 'z' => 1, 'k' => 1, 'l' => 2), $view['params']);
        $this->assertArrayHasKey('b', $view['attrs']);
    }

}

<?php

namespace EMC\TableBundle\Tests\Table\Column\Type;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Table\Column\Type\ButtonType;
use EMC\TableBundle\Table\Column\Column;

/**
 * ButtonYpeTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ButtonTypeTest extends AbstractUnitTest {
    public function testBuildView() {

        $type = new ButtonType();
        $optionsResolver = $type->getOptionsResolver();
        $type->setDefaultOptions($optionsResolver, $this->defaultColumnOptions);
        $options = array(
            'name' => 'foo',
            'text'=> "btn magic"
        );
        
        $resolvedOptions = $optionsResolver->resolve($options);
        $view = array();

        $column = new Column($type, $resolvedOptions);

        $type->buildView($view, $column, array('i' => 1), $resolvedOptions);

        $expectedViewKeys = array(
            'name',
            'type',
            'attrs',
            'value',
            'text',
            'title'
        );

        foreach ($expectedViewKeys as $key) {
            $this->assertArrayHasKey($key, $view);
        }
        $this->assertArrayHasKey('class', $view['attrs']);
        $this->assertEquals('foo', $view['name']);
        $this->assertEquals($view['type'], $type->getName());
        $this->assertEquals($view['text'], $resolvedOptions['text']);
        $this->assertEquals($view['title'], $resolvedOptions['desc']);
    }
}

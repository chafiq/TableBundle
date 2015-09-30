<?php

namespace EMC\TableBundle\Tests\Table\Column\Type;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Table\Column\Type\ImageType;
use EMC\TableBundle\Table\Column\Column;


/**
 * ImageTypeTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class ImageTypeTest extends AbstractUnitTest {
    public function testBuildView() {

        $type = new ImageType();
        $optionsResolver = $type->getOptionsResolver();
        $type->setDefaultOptions($optionsResolver);
        $options = array(
            'name'  => 'foo',
            'asset_url' => 'img.png'
        );
        
        $resolvedOptions = $optionsResolver->resolve($options);
        $view = array();

        $column = new Column($type, $resolvedOptions);

        $type->buildView($view, $column, array(new \DateTime), $resolvedOptions);

        $expectedViewKeys = array(
            'name',
            'type',
            'attrs',
            'value',
            'asset_url',
            'alt',
            'output'
        );

        foreach ($expectedViewKeys as $key) {
            $this->assertArrayHasKey($key, $view);
        }
        $this->assertArrayHasKey('class', $view['attrs']);
        $this->assertEquals('foo', $view['name']);
        $this->assertEquals($view['type'], $type->getName());
        $this->assertEquals($view['asset_url'], $resolvedOptions['asset_url']);
        $this->assertEquals($view['alt'], $resolvedOptions['alt']);
        $this->assertEquals($view['output'], $resolvedOptions['output']);
    }
}

<?php

namespace EMC\TableBundle\Tests\Table\Column\Type;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Table\Column\Type\DatetimeType;
use EMC\TableBundle\Table\Column\Column;

/**
 * DatetimeTypeTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class DatetimeTypeTest extends AbstractUnitTest {
    public function testBuildView() {

        $type = new DatetimeType();
        $optionsResolver = $type->getOptionsResolver();
        $type->setDefaultOptions($optionsResolver);
        $options = array(
            'name'  => 'foo',
            'date_format' => 'Y:m_d\TH-s'
        );
        
        $resolvedOptions = $optionsResolver->resolve($options);
        $view = array();

        $column = new Column($type, $resolvedOptions);
        $date = new \DateTime;
        $type->buildView($view, $column, array($date), $resolvedOptions);
        
        $expectedViewKeys = array(
            'name',
            'type',
            'attrs',
            'value'
        );

        foreach ($expectedViewKeys as $key) {
            $this->assertArrayHasKey($key, $view);
        }
        $this->assertArrayHasKey('class', $view['attrs']);
        $this->assertEquals('foo', $view['name']);
        $this->assertEquals($view['type'], $type->getName());
        $this->assertEquals($view['value'], $date->format('Y:m_d\TH-s'));
    }
}

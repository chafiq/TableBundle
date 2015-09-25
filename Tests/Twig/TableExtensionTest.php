<?php

namespace EMC\TableBundle\Tests\Twig;

use EMC\TableBundle\Tests\AbstractUnitTest;
use EMC\TableBundle\Twig\TableExtension;

/**
 * TableExtensionTest
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableExtensionTest extends AbstractUnitTest {

    /**
     * @var TableExtension
     */
    private $extension;

    /**
     * @var \Twig_Template
     */
    private $extensionTemplateMock;

    public function setUp() {
        $twigMock = $this->getMock('\Twig_Environment');

        $templateMock = $this->getMockBuilder('\Twig_Template')
                            ->disableOriginalConstructor()
                                ->getMock();
        $this->extensionTemplateMock = $this->getMockBuilder('\Twig_Template')
                                        ->disableOriginalConstructor()
                                            ->getMock();

        $this->extension = new TableExtension($twigMock, $templateMock, array(
            'test_widget' => array($this->extensionTemplateMock)
        ));
    }

    public function testGetFunctions() {
        $expectedFunctionsKeys = array('table', 'table_rows', 'table_pages', 'table_cell', 'camel_case_to_option');
        $functions = $this->extension->getFunctions();
        
        foreach( $expectedFunctionsKeys as $function ) {
            $this->assertArrayHasKey($function, $functions);
            $this->assertInstanceOf('\Twig_Function_Method', $functions[$function]);
        }
    }


    public function testGetBlock() {
        $this->assertEquals($this->extensionTemplateMock, $this->invokeMethod($this->extension, 'getBlock', array('test')));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockException() {
        $this->invokeMethod($this->extension, 'getBlock', array('noblock'));
    }

    public function testCamelCaseToOption() {
        $this->assertEquals('camel-case-to-option', $this->extension->camelCaseToOption('camelCaseToOption'));
    }

}

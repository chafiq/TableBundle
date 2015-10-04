<?php

namespace EMC\TableBundle\Tests\Twig;

/**
 * Twig_TemplateMock
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TemplateMock extends \Twig_Template
{
    public function __construct(\Twig_Environment $env)
    {
        parent::__construct($env);
    }

    protected function doDisplay(array $context, array $blocks = array()) {
        
    }

    public function getTemplateName() {
        return 'twigmock';
    }

}
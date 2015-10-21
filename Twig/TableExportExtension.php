<?php

namespace EMC\TableBundle\Twig;

use EMC\TableBundle\Table\TableView;

/**
 * TableExtension
 * 
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableExportExtension extends \Twig_Extension {

    /**
     * @var \Twig_Template
     */
    private $template;

    function __construct($template) {
        $this->template = $template;
    }

    public function getFunctions() {
        return array(
            'table_export' => new \Twig_Function_Method($this, 'render', array(
                'is_safe' => array('all'),
                'needs_environment' => true
                    ))
        );
    }

    /**
     * Render block $block with $table view's data.
     * @param \Twig_Environment $twig
     * @param \EMC\TableBundle\Table\TableView $view
     * @return string
     */
    public function render(\Twig_Environment $twig, TableView $view, $template=null) {
        $context = array_merge($twig->getGlobals(), $view->getData());
        return $twig->loadTemplate($template ?: $this->template)->renderBlock('table', $context);
    }

    public function getName() {
        return 'table_export_extension';
    }

}

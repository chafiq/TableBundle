<?php

namespace EMC\TableBundle\Twig;

use EMC\TableBundle\Table\TableInterface;

/**
 * TableExtension
 * 
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableExtension extends \Twig_Extension {

    /**
     * @var \Twig_Environment 
     */
    private $environment;
    
    /**
     * @var \Twig_Template
     */
    private $template;

    /**
     * @var array
     */
    private $extensions;
    
    function __construct(\Twig_Environment $environment, $template, array $extensions) {
        $this->environment = $environment;
        $this->template = $template;
        $this->extensions = $extensions;
    }

    public function load() {
        if ( $this->template instanceof \Twig_Template ) {
            return;
        }
        
        $this->template = $this->environment->loadTemplate($this->template);
        $extensions = array();
        foreach ($this->extensions as $extension) {
            $extensions = array_merge(
                $extensions,
                $this->environment->loadTemplate($extension)->getBlocks()
            );
        }
        $this->extensions = $extensions;
    }
    
    public function getFunctions() {
        return array(
            'table' => new \Twig_Function_Method($this, 'table', array(
                'is_safe' => array('all'),
                'needs_environment' => true
                    )),
            'table_rows' => new \Twig_Function_Method($this, 'rows', array(
                'is_safe' => array('all'),
                'needs_environment' => true
                    )),
            'table_pages' => new \Twig_Function_Method($this, 'pages', array(
                'is_safe' => array('all'),
                'needs_environment' => true
                    )),
            'table_cell' => new \Twig_Function_Method($this, 'cell', array(
                'is_safe' => array('all'),
                'needs_environment' => true
                    )),
            'camel_case_to_option'    => new \Twig_Function_Method($this, 'camelCaseToOption', array(
                'is_safe' => array('all')
                    ))
        );
    }

    public function render(\Twig_Environment $twig, TableInterface $table, $block) {
        $this->load();
        return $this->template->renderBlock($block, $table->getView()->getData());
    }

    public function table(\Twig_Environment $twig, TableInterface $table) {
        return $this->render($twig, $table, 'table');
    }

    public function rows(\Twig_Environment $twig, TableInterface $table) {
        return $this->render($twig, $table, 'rows');
    }

    public function pages(\Twig_Environment $twig, TableInterface $table) {
        return $this->render($twig, $table, 'pages');
    }

    public function cell(\Twig_Environment $twig, array $data) {
        $this->load();
        return $this->getBlock($data['type'])->renderBlock($data['type'] . '_widget', $data);
    }
    
    public function camelCaseToOption($option) {
        return preg_replace('/(?<=\\w)(?=[A-Z])/','-$1', $option);
    }
    
    /**
     * 
     * @param string $type
     * @return type
     * @throws \InvalidArgumentException
     */
    private function getBlock($type) {
        if ( !isset($this->extensions[$type . '_widget']) ) {
            throw new \InvalidArgumentException('Block ' . $type . '_widget for the column type ' . $type . ' not found');
        }
        return $this->extensions[$type . '_widget'][0];
    }

    public function getName() {
        return 'table_extension';
    }

}

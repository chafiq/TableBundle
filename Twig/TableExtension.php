<?php

namespace EMC\TableBundle\Twig;

use EMC\TableBundle\Table\Table;
use EMC\TableBundle\Column\ColumnInterface;

/**
 * TableExtension
 * 
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class TableExtension extends \Twig_Extension {

    /**
     * @var string
     */
    private $template;
    
    /**
     * @var string
     */
    private $extensions;
    
    function __construct($template, $extensions) {
        $this->template = $template;
        $this->extensions = $extensions;
    }
    
    public function getFunctions()
    {
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
            ))
        );
    }
    
    public function render(\Twig_Environment $twig, Table $table, $block) {
        $template = $twig->loadTemplate($this->template);
        return $template->renderBlock($block, $table->getView());
    }
    
    public function table(\Twig_Environment $twig, Table $table) {
        return $this->render($twig, $table, 'table');
    }
    
    public function rows(\Twig_Environment $twig, Table $table) {
        return $this->render($twig, $table, 'rows');
    }
    
    public function pages(\Twig_Environment $twig, Table $table) {
        return $this->render($twig, $table, 'pages');
    }
    
    public function cell(\Twig_Environment $twig, ColumnInterface $column, $data) {
        $template = $twig->loadTemplate($this->extensions);
        $view = $column->getView($data);
        $view['class'] = 'table-' . $column->getExtension();
        return $template->renderBlock($column->getExtension(), $view);
    }
    
    public function getName() {
        return 'table_extension';
    }

}

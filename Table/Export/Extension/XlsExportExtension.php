<?php

namespace EMC\TableBundle\Table\Export\Extension;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Process\Process;
use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\Export\Export;

/**
 * XlsExportExtension
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class XlsExportExtension implements ExportExtensionInterface {

    /**
     * @var string
     */
    private $template;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $twig;

    function __construct(EngineInterface $twig, $template) {
        $this->twig = $twig;
        $this->template = $template;
    }

    public function getIcon() {
        return 'fa fa-file-excel-o';
    }

    public function getName() {
        return 'xls';
    }

    public function getText() {
        return 'EXCEL';
    }

    public function getContentType() {
        return 'application/vnd.ms-excel';
    }

    public function getFileExtension() {
        return 'xls';
    }

    public function export(TableView $view, $template = null, array $options = array()) {
        $out = tempnam('/tmp', 'export-out-');

        $data = $view->getData();

        file_put_contents($out, $this->twig->render($template ? : $this->template, array('table' => $data)));

        $now = new \DateTime();

        $filename = preg_replace(array('/\[now\]/', '/\[caption\]/'), array($now->format('Y-m-d H\hi'), $data['caption']), 'Export');

        return new Export(new \SplFileInfo($out), $this->getContentType(), $filename, $this->getFileExtension());
    }
}

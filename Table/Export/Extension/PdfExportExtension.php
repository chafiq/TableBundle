<?php

namespace EMC\TableBundle\Table\Export\Extension;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Process\Process;
use EMC\TableBundle\Table\TableView;
use EMC\TableBundle\Table\Export\Export;

/**
 * PdfExportExtension
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class PdfExportExtension implements ExportExtensionInterface {

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $bin;

    /**
     * @var array
     */
    private $options;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $twig;

    function __construct(EngineInterface $twig, $template, $bin, array $options) {
        $this->twig = $twig;
        $this->template = $template;
        $this->bin = $bin;
        $this->options = $options;
    }

    public function getIcon() {
        return 'fa fa-file-pdf-o';
    }

    public function getName() {
        return 'pdf';
    }

    public function getText() {
        return 'PDF';
    }

    public function getContentType() {
        return 'application/pdf';
    }

    public function getFileExtension() {
        return 'pdf';
    }

    public function export(TableView $view, $template = null, array $options = array()) {
        try {
            $input = self::tempnam('/tmp', 'export-in-', 'html');
            $output = self::tempnam('/tmp', 'export-out-', 'pdf');

            $data = $view->getData();

            $this->buildHtml($input, $view, $template, $options);
            $this->buildPdf($input, $output, $data);
            
            $filename = $this->formatName($this->options['filename'], new \DateTime(), $data['caption']);
            return new Export(new \SplFileInfo($output), $this->getContentType(), $filename, $this->getFileExtension());
        } finally {
            unlink($input);
        }
        return null;
    }

    private function formatName($name, \DateTime $datetime, $caption) {
        return trim(preg_replace(array('/\[now\]/', '/\[caption\]/'), array($datetime->format('Y-m-d H\hi'), $caption), $name));
    }

    private function buildHtml($filename, TableView $view, $template = null, array $options = array()) {
        file_put_contents($filename, $this->twig->render($template ? : $this->template, array('table' => $view)));
    }

    private function buildPdf($input, $output, array $data) {
        $title = $this->formatName($this->options['title'], new \DateTime(), $data['caption']);

        $cmd = sprintf(
                '%s --lowquality --page-size %s --orientation %s --title "%s" %s %s',
                $this->bin,
                $this->options['page-size'],
                $this->options['orientation'],
                $title,
                $input,
                $output
        );

        $process = new Process($cmd);
        $process->setTimeout($this->options['timeout']);
        $process->run();

        if (!$process->isSuccessful()) {
            unlink($output);
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    private static function tempnam($dir, $prefix, $extension) {
        $tempnam = tempnam($dir, $prefix);
        rename($tempnam, $tempnam . '.' . $extension);
        return $tempnam . '.' . $extension;
    }

}

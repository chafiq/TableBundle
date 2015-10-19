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

    /**
     * {@inheritdoc}
     */
    public function getIcon() {
        return 'fa fa-file-pdf-o';
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function getText() {
        return 'PDF';
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType() {
        return 'application/pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension() {
        return 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function export(TableView $view, $template = null, array $options = array()) {
        $input = $this->tempnam('html');
        $output = $this->tempnam('pdf');
        try {
            $data = $view->getData();

            $this->buildHtml($input, $view, $template, $options);
            $this->buildPdf($input, $output, $data);

            $filename = $this->formatName($this->options['filename'], new \DateTime(), $data['caption']);
            $export = new Export(new \SplFileInfo($output), $this->getContentType(), $filename, $this->getFileExtension());
            
            unlink($input);
            return $export;
        } catch (\Exception $exception) {
            unlink($input);
            throw $exception;
        }
    }

    private function formatName($name, \DateTime $datetime, $caption) {
        return trim(preg_replace(array('/\[now\]/', '/\[caption\]/'), array($datetime->format('Y-m-d H\hi'), $caption), $name));
    }

    /**
     * Render HTML
     * @param string $filename
     * @param \EMC\TableBundle\Table\TableView $view
     * @param string $template Template to use. If not set, the default template will be used
     * @param array $options
     */
    private function buildHtml($filename, TableView $view, $template = null, array $options = array()) {
        file_put_contents($filename, $this->twig->render($template ? : $this->template, array('table' => $view)));
    }

    /**
     * Build the PDF from HTML
     * @param string $input HTML input file path
     * @param string $output PDF output file path
     * @param array $data
     * @throws \RuntimeException
     */
    private function buildPdf($input, $output, array $data) {
        $title = $this->formatName($this->options['title'], new \DateTime(), $data['caption']);

        $cmd = sprintf(
                '%s --lowquality --images --page-size %s --orientation %s --title "%s" %s %s',
                $this->bin,
                $this->options['page-size'],
                $this->options['orientation'],
                $title,
                $input,
                $output
        );

        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            unlink($output);
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * Process for pdf conversion
     * @param string Command for converting html -> pdf
     * @return \Symfony\Component\Process\Process
     */
    private function getProcess($cmd) {
        $process = new Process($cmd);
        $process->setTimeout($this->options['timeout']);
        return $process;
    }

    /**
     * Create a temporary file with specified extension in the directory which is configured in <br/>
     * app/config/config.yml : emc > table > export > pdf > options > dir.<br/>
     * if not configure it take the default value /tmp<br/>
     * If the directory not exists, it will be created
     * @param string $extension File extension
     * @return string Temporary file name with extension
     * @throws \RuntimeException
     */
    private function tempnam($extension) {

        if (!is_dir($this->options['dir'])) {
            if (!mkdir($this->options['dir'], 0775, true)) {
                throw new \RuntimeException('Unable to create directory "' . $this->options['dir'] . '"');
            }
        }

        $tempnam = tempnam($this->options['dir'], 'emc-table-export-');
        rename($tempnam, $tempnam . '.' . $extension);
        return $tempnam . '.' . $extension;
    }

}

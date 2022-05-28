<?php
/**
 * Abstract for generating html as PDF
 * For dompdf, general steps:
 * $dompdf-> load html
 * $dompdf-> set page options
 * $dompdf-> render()
 * $dompdf-> output()
 *
 * @author holly
 */
class ViewSnipets_PDF_Reader {

    const 
        PAGE_SIZE__A4                     = 'A4',
        PAGE_SIZE__LETTER                 = 'letter',
        PAGE_LAYOUT__PORTRAIT             = 'portrait',
        PAGE_LAYOUT__LANDSCAPE            = 'landscape'
        ;

    /**
     * DOMPDF object
     * @var \Dompdf\Dompdf $dompdf
     */
    protected $dompdf                       = null,
              $dompdf_options               = null;

    /**
     * Instantiate dompdf object
     * Instantiate dompdf optiosn object
     * Automatically enable external links for images
     * Automactially set certificate dpi (default is 82) to account for old certificate images
     */
    public function __construct() {
        $this->dompdf = new \Dompdf\Dompdf();
        $this->dompdf_options = new \Dompdf\Options();
    }

    /**
     * Set paper size and layout
     * @param string $paper_size
     * @param string $paper_layout
     */
    protected function setPaperLayout($paper_size = self::PAGE_SIZE__A4, $paper_layout = self::PAGE_LAYOUT__LANDSCAPE) {
        $this->dompdf->setPaper($paper_size, $paper_layout);
    }

    /**
     * Set DPI for PDF reader
     * @param int $dpi
     */
    protected function setDPI($dpi) {
        $this->dompdf_options->setDpi($dpi);
    }

    /**
     * Allow external links for imgs
     * @param string $enabled
     */
    protected function setIsRemoteEnabled($enabled = false) {
        $this->dompdf_options->setIsRemoteEnabled($enabled);
    }

    /**
     * Load html
     * @param string $html
     */
    protected function loadHTML($html) {
        $this->dompdf->load_html($html);
    }

    /**
     * Load html file
     * @param file path $html_file
     */
    protected function loadHTMLFile($html_file) {
        $this->dompdf->load_html_file($html_file);
    }

    /**
     * Render
     * Make sure any options are set
     */
    protected function render() {
        $this->dompdf->setOptions($this->dompdf_options);
        $this->dompdf->render();
    }

    /**
     * Output as PDF to download or in separate tab/page
     * @param string $pdf_filename
     * @param bool $download
     *      - true: download
     *      - false: separate tab/page
     */
    protected function stream($pdf_filename, $download = true) {
        $this->render();
        $this->dompdf->stream($pdf_filename, ["Attachment" => $download]);
    }
}

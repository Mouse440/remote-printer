<?php
/*
 * $Id: split_document.php,v 1.2 2013/02/22 21:39:25 rp Exp $
 * 
 * Split PDF document:
 * Split PDF document in seperate output documents where each output
 * document contains a range of one or more pages from the input document.
 * 
 * Due to the limitation that the PHP script can only send one file at
 * a time as a response over the web, only the first output document is
 * returned.
 *
 * Interactive elements (e.g. bookmarks) will be dropped.
 *
 * Required software: PDFlib+PDI/PPS 9
 * Required data: PDF input document
 */

/* This is where the data files are. Adjust as necessary. */
$searchpath = dirname(dirname(dirname(__FILE__)))."/input";
$outfile_basename = "split_document";
$title = "Split PDF Document";
$infile = "PDFlib-datasheet.pdf";

/*
 * Document will be split into sub-documents where each document has
 * this many pages (except the last sub-document potentially).
 */
define("SUBDOC_PAGES", 2);

try {
    $p = new pdflib();

    $p->set_option("searchpath={" . $searchpath . "}");

    /* This means we must check return values of load_font() etc. */
    $p->set_option("errorpolicy=return");
    $p->set_option("stringformat=utf8");

    $indoc = $p->open_pdi_document($infile, "");
    if ($indoc == 0)
        throw new Exception("Error: " . $p->get_errmsg());

    /*
     * Determine the number of pages in the input document and compute
     * the number of output documents.
     */
    $page_count = (int) $p->pcos_get_number($indoc, "length:pages");
    $outdoc_count = $page_count / SUBDOC_PAGES
            + ($page_count % SUBDOC_PAGES > 0 ? 1 : 0);

    /*
     * The loop only produces a single output document that is returned over
     * HTTP.
     * 
     * For producing all output documents, change the loop condition like this:
     * 
     *      $outdoc_counter < $outdoc_count
     */
    for ($outdoc_counter = 0, $page = 0;
            $outdoc_counter < 1; $outdoc_counter += 1) {
        $outfile = $outfile_basename . "_" . ($outdoc_counter + 1) . ".pdf";

        /*
         * Open new sub-document.
         */
        if ($p->begin_document("", "") == 0)
            throw new Exception("Error: " . $p->get_errmsg());

        $p->set_info("Creator", "PDFlib Cookbook");
        $p->set_info("Title", $title . ' $Revision: 1.2 $');
        $p->set_info("Subject", "Sub-document " . ($outdoc_counter + 1)
            . " of " . $outdoc_count . " of input document '" . $infile . "'");

        for ($i = 0; $page < $page_count && $i < SUBDOC_PAGES;
                                            $page += 1, $i += 1) {
            /* Dummy page size; will be adjusted later */
            $p->begin_page_ext(10, 10, "");

            $pagehdl = $p->open_pdi_page($indoc, $page + 1, "");
            if ($pagehdl == 0)
                throw new Exception("Error opening page: " . $p->get_errmsg());

            /*
             * Place the imported page on the output page, and adjust
             * the page size
             */
            $p->fit_pdi_page($pagehdl, 0, 0, "adjustpage");
            $p->close_pdi_page($pagehdl);

            $p->end_page_ext("");
        }

        /* Close the current sub-document */
        $p->end_document("");

        /*
         * Return the sub-document to the user. If all split documents are to
         * be processed, do something different, e.g. write the documents
         * to disk and create an HTML page with a list of links for the
         * sub-documents.
         */
        $buf = $p->get_buffer();
        $len = strlen($buf);
    
        header("Content-type: application/pdf");
        header("Content-Length: $len");
        header("Content-Disposition: inline; filename=" . $outfile);
        print $buf;
    }

    /* Close the input document */
    $p->close_pdi_document($indoc);
}
catch (PDFlibException $e) {
    die("PDFlib exception occurred:\n" .
        "[" . $e->get_errnum() . "] " . $e->get_apiname() . ": " .
        $e->get_errmsg() . "\n");
}
catch (Exception $e) {
    die($e);
}

$p = 0;

?>
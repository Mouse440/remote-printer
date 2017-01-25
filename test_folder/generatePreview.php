<?php
require_once(__DIR__."/../php/util/show-error.php");
require_once(__DIR__."/../php/config/Config.php");
require_once(__DIR__."/../php/util/PreviewGenerator.php");
// require_once(__DIR__."/../php/util/FileUtilities.php");

$prefix = "doc1";
$previewG = new PreviewGenerator($prefix,"1137");

echo( json_encode( array("preview-links" => $previewG->getPreviewImageLinks('1-2,4-8,9',true) ) ) );

?>
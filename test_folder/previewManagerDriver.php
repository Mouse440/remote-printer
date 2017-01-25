<?php
	require_once(__DIR__."/../php/config/Config.php");
	require_once(__DIR__."/../php/util/PreviewManagerStrategy.php");

	$previewManger = new PreviewManagerStrategy(
								new ReflectionClass('Config'),
								'doc9',
								'ppt',
								'4');

	echo $previewManger->getPreviewLinks(true,'1,4','landscape');
?>
<?php 
require __DIR__.'/../gcp/GoogleCloudPrint.php';
require __DIR__.'/../php/phase2/GCPPrintAdapter.php';

$gcpPrinterAdapter = new GCPPrintAdapter(
		array(
			'fileToPrintFullPath' => 'gcp/pdf.pdf', 
			'filePrefix' => 'testingfcghvajbdknsflmgdbjhsbfknlgmhbdfjsknlmgd;hkbdfjsaknlmfghjbdfsknalmkfgjdnflsm;a,kfjdg',
			'copies' => '2'
		)
	);

echo $gcpPrinterAdapter->executePrint();


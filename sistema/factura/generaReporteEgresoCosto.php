<?php
	include "../../conexion.php";
	require_once '../pdf/autoload.inc.php';
	use Dompdf\Dompdf;
	use Dompdf\Options;

	
	ob_start();
	$fechaIni =	$_REQUEST['fechaIni'];
	$fechaFin = $_REQUEST['fechaFin'];
	include(dirname('__FILE__').'/reporteEgresoCosto.php');
	$html = ob_get_clean();
	
	// instantiate and use the dompdf class
	$dompdf = new Dompdf();
	$options = new Options(); 
	$options->set('isRemoteEnabled', true); 
	$dompdf = new Dompdf($options);

	$dompdf->loadHtml($html);
	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('letter', 'portrait');
	// Render the HTML as PDF
	//Donde guardar el documento

	$nombreArchivo = "reportesPDF/reporteCosto.pdf";
	$dompdf->render();
	//Guardalo en una variable
	$output = $dompdf->output();
	file_put_contents( $nombreArchivo, $output);

	
?>
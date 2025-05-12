<?php

	//print_r($_REQUEST);
	//exit;
	//echo base64_encode('2');
	//exit;
	session_start();
	if(empty($_SESSION['active']))
	{
		header('location: ../');
	}

	include "../../conexion.php";
	require_once '../pdf/autoload.inc.php';
	use Dompdf\Dompdf;
	use Dompdf\Options;

	if(empty($_REQUEST['cl']) || empty($_REQUEST['f']))
	{
		echo "No es posible generar la factura.";
	}else{
		$codCliente = $_REQUEST['cl'];
		$noFactura = $_REQUEST['f'];
		$anulada = '';

		$query_config   = mysqli_query($conexion,"SELECT * FROM configuracion");
		$result_config  = mysqli_num_rows($query_config);
		if($result_config > 0){
			$configuracion = mysqli_fetch_assoc($query_config);
		}


		$query = mysqli_query($conexion,"SELECT f.id_comprobante, DATE_FORMAT(f.com_fechaemi, '%d/%m/%Y') as fecha, DATE_FORMAT(f.com_fechaemi,'%H:%i:%s') as  hora, f.id_cliente, f.com_estado,
												v.usu_nombre as vendedor,
												cl.cli_documento, cl.cli_nombre, cl.cli_telefono,cl.cli_direccion,tc.id_tc, tc.nombre
											FROM comprobante f
											INNER JOIN usuario v
											ON f.id_usuario = v.id_usuario
											INNER JOIN cliente cl
											ON f.id_cliente = cl.id_cliente INNER JOIN ti_comprobante tc ON tc.id_tc = f.id_tc
											WHERE f.id_comprobante = $noFactura AND f.id_cliente = $codCliente  AND f.com_estado != 10 ");

		$result = mysqli_num_rows($query);
		if($result > 0){

			$factura = mysqli_fetch_assoc($query);
			$no_factura = $factura['id_comprobante'];

			if($factura['com_estado'] == 2){
				$anulada = '<img class="anulada" src="https://ws.net.pe/sistema/factura/img/anulado.png" alt="Anulada">';
			}

			$query_productos = mysqli_query($conexion,"SELECT p.servi_nombre,p.tiempo,dt.temp_preciototal as precio_total
														FROM comprobante f 
														INNER JOIN detalle_comprobante dt
														ON f.id_comprobante = dt.id_comprobante
														INNER JOIN servicio p
														ON dt.cod_servicio = p.cod_servicio
														WHERE f.id_comprobante = $no_factura ");

			$result_detalle = mysqli_num_rows($query_productos);

			ob_start();
		    include(dirname('__FILE__').'/factura.php');
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
			$dompdf->render();
			// Output the generated PDF to Browser
			$dompdf->stream('factura_'.$noFactura.'.pdf',array('Attachment'=>0));
			exit;
		}
	}

?>
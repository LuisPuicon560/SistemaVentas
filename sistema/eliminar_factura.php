<?php
session_start();
//PARA AGREGAR A QUE PUEDAN ELIMINAR OTRO ROL, SE LE AGREGA and $_sESSION['rol'] !=2
//ELIMINAR REGISTROS
    include "../conexion.php";
    if(!empty($_POST)){

        if(empty($_POST['idfactura'])){

            header("Location: ventas.php");
            mysqli_close($conexion);

        }

        $idfactura = $_POST['idfactura'];   
        //SCREAM ELIMINAR
        //$query_delete = mysqli_query($conexion,"DELETE FROM usuario WHERE id_usuario=$idusuario");
        //SCREAM ESTADO DE REGISTRO
        $query_delete = mysqli_query($conexion,"UPDATE comprobante SET com_estado = 2 WHERE id_comprobante = $idfactura");
        mysqli_close($conexion);
        if($query_delete){
            header("Location: ventas.php");
        }else{
            echo "Error al anular la Factura.";
        }

    }
    //PARA QUE NO PODAMOS BUSCAR LOS REGISTROS DE ACUERDO A LA URL QUE SE BRINDA
    if(empty($_REQUEST['id']) ){
        header("Location: listar_cliente.php");
       // mysqli_close($conexion);
    }else{
        include "../conexion.php";
        
        $idfactura = $_REQUEST['id'];

        $query = mysqli_query($conexion, "SELECT c.id_comprobante, c.com_fechaemi, cl.cli_nombre, u.usu_nombre, c.com_totalfactura 
                                        FROM comprobante c INNER JOIN cliente cl ON c.id_cliente=cl.id_cliente INNER JOIN usuario u ON c.id_usuario = u.id_usuario
                                        WHERE c.id_comprobante = $idfactura");
        
        mysqli_close($conexion);
        $result = mysqli_num_rows($query);

        if ($result > 0){
            
            while($data = mysqli_fetch_array($query)){

                $ncomprobante = $data['id_comprobante'];
                $fecha = $data['com_fechaemi'];
                $nombre = $data['cli_nombre'];
                $usuario = $data['usu_nombre'];
                $total = $data['com_totalfactura'];
                
            }
        }else{
            header("Location: ventas.php");
        }
    }


?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Eliminar Cliente</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<div class="data_delete">
        <i class="fas fa-cubes fa-7x" style="color: #DA6550"></i>
         <br>
         <br>
            <h2>¿Esta seguro de anular la Factura?</h2>
            <p>Nº Factura: <span><?php echo $ncomprobante; ?></span></p>
            <p>Fecha de Emision: <span><?php echo $fecha; ?></span></p>
            <p>Cliente: <span><?php echo $nombre; ?></span></p>
            <p>Vendedor: <span><?php echo $usuario; ?></span></p>
            <p>Total de Factura: <span><?php echo $total; ?></span></p>
            <form method="post" action="">
                <input type="hidden" name="idfactura" value="<?php echo $idfactura;?>">
                <a href="ventas.php" class="btn_cancelar"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Anular</button>
            </form>
        </div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
<?php
session_start();
//PARA AGREGAR A QUE PUEDAN ELIMINAR OTRO ROL, SE LE AGREGA and $_sESSION['rol'] !=2
//ELIMINAR REGISTROS
    include "../conexion.php";
    if(!empty($_POST)){

        if(empty($_POST['idcliente'])){

            header("Location: listar_cliente.php");
            mysqli_close($conexion);

        }

        $idcliente = $_POST['idcliente'];   
        //SCREAM ELIMINAR
        //$query_delete = mysqli_query($conexion,"DELETE FROM usuario WHERE id_usuario=$idusuario");
        //SCREAM ESTADO DE REGISTRO
        $query_delete = mysqli_query($conexion,"UPDATE cliente SET cli_estado = 0 WHERE id_cliente = $idcliente");
        mysqli_close($conexion);
        if($query_delete){
            header("Location: listar_cliente.php");
        }else{
            echo "Error al eliminar el registro.";
        }

    }
    //PARA QUE NO PODAMOS BUSCAR LOS REGISTROS DE ACUERDO A LA URL QUE SE BRINDA
    if(empty($_REQUEST['id']) ){
        header("Location: listar_cliente.php");
       // mysqli_close($conexion);
    }else{
        include "../conexion.php";
        
        $idcliente = $_REQUEST['id'];

        $query = mysqli_query($conexion, "SELECT u.usu_nombre, c.cli_documento, c.cli_nombre, c.cli_telefono,c.cli_direccion 
                                        FROM cliente c INNER JOIN usuario u ON c.id_usuario=u.id_usuario WHERE c.id_cliente = $idcliente");
        
        mysqli_close($conexion);
        $result = mysqli_num_rows($query);

        if ($result > 0){
            
            while($data = mysqli_fetch_array($query)){

                $usuario = $data['usu_nombre'];
                $documento = $data['cli_documento'];
                $nombre = $data['cli_nombre'];
                $telefono = $data['cli_telefono'];
                $direccion = $data['cli_direccion'];
                
            }
        }else{
            header("Location: listar_cliente.php");
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
        <i class="fas fa-user-times fa-7x" style="color: #DA6550"></i>
         <br>
         <br>
            <h2>¿Esta seguro de eliminar el siguiente registro?</h2>
            <p>Usuario que lo registro: <span><?php echo $usuario; ?></span></p>
            <p>Nº de Documento: <span><?php echo $documento; ?></span></p>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Telefono: <span><?php echo $telefono; ?></span></p>
            <p>Direccion: <span><?php echo $direccion; ?></span></p>
            <form method="post" action="">
                <input type="hidden" name="idcliente" value="<?php echo $idcliente;?>">
                <a href="listar_cliente.php" class="btn_cancelar"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
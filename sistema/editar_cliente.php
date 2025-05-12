<?php
//DAR PERMISOS PARA QUE INGRESEN A LAS VISTAS
session_start();

include "../conexion.php";
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['documento']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']) || empty($_POST['fecha'])){

            $alert = '<p class="msg_error"> Todos los campos son obligatorios.</p>';
        }else{

            $idcliente = $_POST['idcliente'];
            $documento = $_POST['documento'];
            $nombre  = $_POST['nombre'];
            $telefono   = $_POST['telefono'];
            $direccion  = $_POST['direccion'];
            $fecha  = $_POST['fecha'];

            $result = 0;
            //QUE PERMITA POR MAS QUE INGRESEMOS UN VALOR DE 0 
            if(is_numeric($documento) and $documento != 0){
                
                //PARA QUE NO SE REPITAN
                $query = mysqli_query($conexion,"SELECT * FROM cliente WHERE (cli_documento = '$documento' AND id_cliente != '$idcliente')");
            
                $result = mysqli_fetch_array($query);
                //$result = count($result);
            }
            if($result > 0){
                $alert = '<p class="msg_error"> El Numero de Documento ya existe, opta por otro</p>';
            }else{
                if($documento == ''){

                    $documento = 0;

                }

                $sql_update = mysqli_query($conexion,"UPDATE cliente SET  
                                                            cli_documento = '$documento', cli_nombre = '$nombre', 
                                                            cli_telefono = '$telefono', cli_direccion = '$direccion', cli_fechagr ='$fecha' WHERE id_cliente = $idcliente ");

                if($sql_update){

                    $alert = '<p class="msg_save">Cliente actualizado correctamente.</p>';

                }else{

                    $alert = '<p class="msg_error"> Error al actualizar el cliente, intentelo de nuevo.</p>';
                }
            }
        }
        
    }
//MOSTRAR DATOS
    if(empty($_REQUEST['id'])){
        header('Location: listar_cliente.php ');
        
    }
    $idcliente = $_REQUEST['id'];

    $sql = mysqli_query($conexion, "SELECT c.id_cliente, c.cli_documento,c.cli_nombre,c.cli_telefono, c.cli_direccion,cli_fechagr FROM cliente c
                                    WHERE id_cliente= $idcliente and cli_Estado = 1");


    
    $result_sql = mysqli_num_rows($sql);
    if ($result_sql == 0) {  
        header("Location: listar_cliente.php");
    }else{
        $option='';
        while ($data = mysqli_fetch_array($sql)){

            $idcliente = $data['id_cliente'];
            $documento = $data['cli_documento'];
            $nombre = $data['cli_nombre'];
            $telefono = $data['cli_telefono'];
            $direccion = $data['cli_direccion'];
            $fecha = $data['cli_fechagr'];
        }
        //PARA QUE NOS MUESTRE DEAFULT EL ROL (SI HAY UN ROL MAS, SE LE AGREGA UN ELSE IF MAS)
        
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>ACTUALIZAR USUARIO | WEBSITE</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <div class="form_register">
            <h1>Editar Cliente</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : '';?></div>
            <form action="" method="post">
                <input type="hidden" name="idcliente" value="<?php echo $idcliente;?>">
                <label for="documento">Nº Documento:</label>
                <input type="number" name="documento" id="documento" placeholder="Numero de Documento" value="<?php echo $documento;?>">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre Cliente" value="<?php echo $nombre;?>">
                <label for="telefono">Telefono:</label>
                <input type="number" name="telefono" id="telefono" placeholder="Telefono Cliente" value="<?php echo $telefono;?>">
                <label for="direccion">Direccion:</label>
                <input type="text" name="direccion" id="direccion" placeholder="Direccion" value="<?php echo $direccion;?>">
                <label for="fecha">Fecha de Inicio de Contrato:</label>
                <input type="datetime" name="fecha" id="fecha" placeholder="Fecha de Inicio de Contrato" value="<?php echo $fecha;?>">
                <button type="submit" class="btn_save"><i class="far fa-save"></i> Modificar Cliente</button>
                <a href="listar_cliente.php" class="btn_atras"><i class="fa fa-backward"></i> Atras</a>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
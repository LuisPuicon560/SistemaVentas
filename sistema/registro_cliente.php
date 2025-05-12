<?php
//PROHIBIR ACCESOS QUE NO SEAN DE ADMIN
session_start();
date_default_timezone_set('America/Lima');
include "../conexion.php";
    if(!empty($_POST)){
        $alert='';
        if(/*empty($_POST['tipodoc']) ||*/ empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])){

            $alert = '<p class="msg_error"> Todos los campos son obligatorios.</p>';
        }else{

            $documento = $_POST['documento'];
            //$tipodoc = $_POST['tipodoc'];
            $nombre  = $_POST['nombre'];
            $telefono   = $_POST['telefono'];
            $direccion  = $_POST['direccion'];
            $usuario_id = $_SESSION['usuario_id'];

//NO SE REPITA EL NUMERO DE DOCUMENTO
            $result = 0;
            if(is_numeric($documento) && $documento !=0){

                $query = mysqli_query($conexion,"SELECT * FROM cliente WHERE cli_documento = '$documento'");
                $result = mysqli_fetch_array($query);
            }
            if($result >0){
                $alert = '<p class="msg_error"> El numero de documento ya existe.</p>';
            }else{
                $query_insert = mysqli_query($conexion,"INSERT INTO cliente(cli_documento,cli_nombre,cli_telefono,cli_direccion,id_usuario) 
                                                        VALUES('$documento','$nombre','$telefono','$direccion','$usuario_id') ");
                if($query_insert){
                    $alert = '<p class="msg_save">Cliente guardado correctamente.</p>';
                }else{
                    $alert = '<p class="msg_error"> Error al guardar el cliente, intentelo de nuevo.</p>';
                }
            }

        }
        //mysqli_close($conexion);
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
	<?php include "includes/scripts.php";?>
	<title>REGISTRO CLIENTE | WEBSITE</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <div class="form_register">
            <h1> Registro Cliente</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : '';?></div>
            <form action="" method="post">
                <label for="documento">Nº de Documento:</label>
                <input type="number" name="documento" id="documento" placeholder="Ingrese el numero de documento">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre">
                <label for="telefono">Telefono:</label>
                <input type="number" name="telefono" id="telefono" placeholder="Ingrese el telefono">
                <label for="direccion">Direccion:</label>
                <input type="text" name="direccion" id="direccion" placeholder="Ingrese la direccion">
                
                <button type="submit" class="btn_save"><i class="far fa-save"></i> Agregar Cliente</button>
                <a href="index.php" class="btn_atras"><i class="fa fa-backward"></i> Atras</a>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
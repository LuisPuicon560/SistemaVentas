<?php
//PROHIBIR ACCESOS QUE NO SEAN DE ADMIN
session_start();

include "../conexion.php";
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['tiposer']) || empty($_POST['tiempo']) || $_POST['precio'] <= 0 || empty($_POST['precio']) || $_POST['precio'] <= 0){

            $alert = '<p class="msg_error"> Todos los campos son obligatorios.</p>';
        }else{
            $nombre  = $_POST['nombre'];
            $tiposer   = $_POST['tiposer'];
            $precio  = $_POST['precio'];
            $tiempo  = $_POST['tiempo'];
            $usuario_id = $_SESSION['usuario_id'];

//NO SE REPITA EL NUMERO DE DOCUMENTO
            $result = 0;
            if(is_numeric($nombre) && $nombre =0){

                $query = mysqli_query($conexion,"SELECT * FROM servicio WHERE servi_nombre = '$nombre'");
                $result = mysqli_fetch_array($query);
            }
            if($result >0){
                $alert = '<p class="msg_error"> El codigo del Servicio ya existe.</p>';
            }else{
                $query_insert = mysqli_query($conexion,"INSERT INTO servicio(servi_nombre, id_tiposer, servi_precio,tiempo, id_usuario) 
                                                        VALUES('$nombre','$tiposer','$precio','$tiempo','$usuario_id') ");
                if($query_insert){
                    $alert = '<p class="msg_save">Servicio guardado correctamente.</p>';
                }else{
                    $alert = '<p class="msg_error"> Error al guardar el Servicio, intentelo de nuevo.</p>';
                }
            }

        }
        //mysqli_close($conexion);
    }
    date_default_timezone_set("America/Lima");


?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>REGISTRO SERVICIO | WEBSITE</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <div class="form_register">
            <h1> Registro Servicio</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : '';?></div>
            <!--PARA QUE NUESTRO FORM PUEDA ADJUNTAR ARCHIVOS SE USA enctype="multipart/form-data"-->
            <form action="" method="post">
                <label for="nombre">Nombre del Servicio:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre del servicio">
                <label for="tiposer"> Tipo de Servicio:</label>
                <?php

                    $query_tiposer = mysqli_query($conexion,"SELECT * FROM tipo_servicio");
                    //
                    $result_tiposer  = mysqli_num_rows($query_tiposer);

                ?>
                
                <select name="tiposer" id="tiposer">
                    <?php
                        if($result_tiposer > 0){
                            
                            while ($tiposer =  mysqli_fetch_array($query_tiposer)){
                    ?>
                    <option value="<?php echo $tiposer['id_tiposer']?>"><?php echo $tiposer['tise_nombre']?></option>
                    
                    <?php
                            }
                        }
                    ?>
                </select>
                <label for="tiempo">Contrato (Meses):</label>
                <input type="text" name="tiempo" id="tiempo" placeholder="Ingrese el contrato (meses)">
                <label for="precio">Precio:</label>
                <input type="text" name="precio" id="precio" placeholder="Ingrese el precio">
                        
                <button type="submit" class="btn_save"><i class="far fa-save"></i> Agregar Servicio</button>
                <a href="index.php" class="btn_atras"><i class="fa fa-backward"></i> Atras</a>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
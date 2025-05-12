<?php
//PROHIBIR ACCESOS QUE NO SEAN DE ADMIN
session_start();
include "../conexion.php";
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['rol'])){

            $alert = '<p class="msg_error"> Todos los campos son obligatorios.</p>';
        }else{

            

            $nombre = $_POST['nombre'];
            $email  = $_POST['correo'];
            $user   = $_POST['usuario'];
            $clave  = md5($_POST['clave']);
            $rol    = $_POST['rol'];

            //PARA QUE NO SE REPITAN
            $query = mysqli_query($conexion,"SELECT * FROM usuario WHERE usu_usuario = '$user' OR usu_correo = '$email'");
            
            $result = mysqli_fetch_array($query);

            if($result > 0){
                $alert = '<p class="msg_error"> El usuario o correo ya existen, opta por otro</p>';
            }else{
                $query_insert = mysqli_query($conexion,"INSERT INTO usuario(usu_nombre,usu_correo,usu_usuario,usu_clave,id_rol) 
                                                        VALUES('$nombre','$email','$user','$clave','$rol') ");
                if($query_insert){
                    $alert = '<p class="msg_save">Usuario creado correctamente.</p>';
                }else{
                    $alert = '<p class="msg_error"> Error al crear el usuario, intentelo de nuevo.</p>';
                }
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8"> 
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
	<?php include "includes/scripts.php";?>
	<title>REGISTRO USUARIOS</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <div class="form_register">
            <h1> Registro Usuario</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : '';?></div>
            <form action="" method="post">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo">
                <label for="correo">Correo Electronico:</label>
                <input type="email" name="correo" id="correo" placeholder="Correo electronico">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" placeholder="Nombre usuario">
                <label for="clave">Contraseña:</label>
                <input type="password" name="clave" id="clave" placeholder="Contraseña">
                <label for="rol"> Tipo de Usuario</label>
                <!--QUERY QUE NOS DEVUELVA TODO LOS ROLES-->
                <?php

                    $query_rol = mysqli_query($conexion,"SELECT * FROM rol");
                    //
                    $result_rol  = mysqli_num_rows($query_rol);

                ?>
                
                <select name="rol" id="rol">
                    <?php
                        if($result_rol > 0){
                            
                            while ($rol =  mysqli_fetch_array($query_rol)){
                    ?>
                    <option value="<?php echo $rol['id_rol']?>"><?php echo $rol['rol']?></option>
                    
                    <?php
                            }
                        }
                    ?>
                </select>
                <button type="submit" class="btn_save"><i class="far fa-save"></i> Crear Usuario</button>
                <a href="index.php" class="btn_atras"><i class="fa fa-backward"></i> Atras</a>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
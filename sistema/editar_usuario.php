<?php
//DAR PERMISOS PARA QUE INGRESEN A LAS VISTAS
session_start();

include "../conexion.php";
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['rol'])){

            $alert = '<p class="msg_error"> Todos los campos son obligatorios.</p>';
        }else{

            $idusuario = $_POST['idusuario'];
            $nombre = $_POST['nombre'];
            $email  = $_POST['correo'];
            $user   = $_POST['usuario'];
            $clave  = md5($_POST['clave']);
            $rol    = $_POST['rol'];

            //PARA QUE NO SE REPITAN
            $query = mysqli_query($conexion,"SELECT * FROM usuario WHERE (usu_usuario = '$user'AND id_usuario != $idusuario)
                                    OR (usu_correo = '$email' AND id_usuario != $idusuario)");
            
            $result = mysqli_fetch_array($query);
            //$result = count($result);

            if($result > 0){
                $alert = '<p class="msg_error"> El usuario o correo ya existen, opta por otro</p>';
            }else{
                if(empty($_POST['clave'])){

                    $sql_update = mysqli_query($conexion,"UPDATE usuario 
                                                            SET usu_nombre = '$nombre', usu_correo = '$email', 
                                                            usu_usuario = '$user', id_rol = '$rol' WHERE id_usuario = $idusuario ");
        
                }else{

                    $sql_update = mysqli_query($conexion,"UPDATE usuario SET usu_nombre = '$nombre', 
                                                            usu_correo = '$email', usu_usuario = '$user' , 
                                                            usu_clave = '$clave', id_rol = '$rol' WHERE id_usuario = $idusuario ");
                }

                if($sql_update){

                    $alert = '<p class="msg_save">Usuario actualizado correctamente.</p>';

                }else{

                    $alert = '<p class="msg_error"> Error al actualizar el usuario, intentelo de nuevo.</p>';
                }
            }
        }
        
    }
//MOSTRAR DATOS
    if(empty($_REQUEST['id'])){
        header('Location: listar_usuario.php');
        
    }
    $iduser = $_REQUEST['id'];

    $sql = mysqli_query($conexion, "SELECT u.id_usuario, u.usu_nombre, u.usu_correo, u.usu_usuario, (u.id_rol) 
                                    AS idrol, (r.rol) AS nombrerol FROM usuario u INNER JOIN rol r ON u.id_rol=r.id_rol 
                                    WHERE id_usuario= $iduser and usu_estado = 1");

    
    $result_sql = mysqli_num_rows($sql);
    if ($result_sql == 0) {  
        header('Location: listar_usuario.php');
    }else{
        $option='';
        while ($data = mysqli_fetch_array($sql)){

            $iduser = $data['id_usuario'];
            $nombre = $data['usu_nombre'];
            $correo = $data['usu_correo'];
            $usuario = $data['usu_usuario'];
            $id_rol = $data['idrol'];
            $rol = $data['nombrerol'];
        }
        //PARA QUE NOS MUESTRE DEAFULT EL ROL (SI HAY UN ROL MAS, SE LE AGREGA UN ELSE IF MAS)
        if($id_rol == 1){
            $option = '<option value="'.$id_rol.'"select>'.$rol.'</option>';
        }else if($id_rol == 2){
            $option = '<option value="'.$id_rol.'"select>'.$rol.'</option>';
        }
    }
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>EDITAR USUARIO</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <div class="form_register">
            <h1>Editar Usuario</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : '';?></div>
            <form action="" method="post">
                <input type="hidden" name="idusuario" value="<?php echo $iduser;?>">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo $nombre;?>">
                <label for="correo">Correo Electronico:</label>
                <input type="email" name="correo" id="correo" placeholder="Correo electronico" value="<?php echo $correo;?>">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" placeholder="Nombre usuario" value="<?php echo $usuario;?>">
                <label for="clave">Contraseña:</label>
                <input type="password" name="clave" id="clave" placeholder="Nueva Contraseña">
                <label for="rol"> Tipo de Usuario</label>
                <!--QUERY QUE NOS DEVUELVA TODO LOS ROLES-->
                <?php
                    include "../conexion.php"; 
                    $query_rol = mysqli_query($conexion,"SELECT * FROM rol");
                     
                    $result_rol  = mysqli_num_rows($query_rol);

                ?>
                
                <select name="rol" id="rol" class="notItemOne">
                    <?php
                    echo $option;
                        if($result_rol > 0){
                            
                            while ($rol =  mysqli_fetch_array($query_rol)){
                    ?>
                    <option value="<?php echo $rol['id_rol']?>"><?php echo $rol['rol']?></option>
                    
                    <?php
                            }
                        }
                    ?>
                </select>
                <button type="submit" class="btn_save"><i class="far fa-save"></i> Modificar Usuario</button>
                <a href="listar_usuario.php" class="btn_atras"><i class="fa fa-backward"></i> Atras</a>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
<?php
//DAR PERMISOS PARA QUE INGRESEN A LAS VISTAS
session_start();
if($_SESSION['rol'] != 1){

	header("Location: ./");

}
//ELIMINAR REGISTROS
    include "../conexion.php";
    if(!empty($_POST)){
        //PARA QUE NO SE CAMBIE EL ID DE ACUERDO CON LA OPCION DE INSPECCIONAR 
        if($_POST['idusuario'] == 1) {
            header("Location: listar_usuario.php");
            mysqli_close($conexion);
            exit;
        }
        $idusuario = $_POST['idusuario'];   
        //SCREAM ELIMINAR
        //$query_delete = mysqli_query($conexion,"DELETE FROM usuario WHERE id_usuario=$idusuario");
        //SCREAM ESTADO DE REGISTRO
        $query_delete = mysqli_query($conexion,"UPDATE usuario SET usu_estado = 0 WHERE id_usuario = $idusuario");
        mysqli_close($conexion);
        if($query_delete){
            header("Location: listar_usuario.php");
        }else{
            echo "Error al eliminar el registro.";
        }

    }
    //PARA QUE NO PODAMOS BUSCAR LOS REGISTROS DE ACUERDO A LA URL QUE SE BRINDA
    if(empty($_REQUEST['id']) || $_REQUEST['id'] == 1){
        header("Location: listar_usuario.php");
        mysqli_close($conexion);
    }else{
        include "../conexion.php";
        
        $idusuario = $_REQUEST['id'];

        $query = mysqli_query($conexion, "SELECT u.usu_nombre, u.usu_usuario, r.rol 
                                        FROM usuario u INNER JOIN rol r ON u.id_rol=r.id_rol WHERE u.id_usuario = $idusuario");
        
        mysqli_close($conexion);
        $result = mysqli_num_rows($query);

        if ($result > 0){
            
            while($data = mysqli_fetch_array($query)){
                $nombre = $data['usu_nombre'];
                $usuario = $data['usu_usuario'];
                $rol = $data['rol'];
            }
        }else{
            header("Location: listar_usuario.php");
        }
    }


?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Eliminar Usuario</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<div class="data_delete">
        <i class="fas fa-user-times fa-7x" style="color: #DA6550"></i>
         <br>
         <br>
            <h2>¿Esta seguro de eliminar el siguiente registro?</h2>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Usuario: <span><?php echo $usuario; ?></span></p>
            <p>Tipo Usuario: <span><?php echo $rol; ?></span></p>
            <form method="post" action="">
                <input type="hidden" name="idusuario" value="<?php echo $idusuario;?>">
                <a href="listar_usuario.php" class="btn_cancelar"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
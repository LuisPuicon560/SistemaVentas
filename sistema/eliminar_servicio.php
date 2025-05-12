<?php
session_start();
//PARA AGREGAR A QUE PUEDAN ELIMINAR OTRO ROL, SE LE AGREGA and $_sESSION['rol'] !=2
//ELIMINAR REGISTROS
    include "../conexion.php";
    if(!empty($_POST)){

        if(empty($_POST['idservicio'])){

            header("Location: listar_servicio.php");
            mysqli_close($conexion);

        }

        $idservicio = $_POST['idservicio'];   
        //SCREAM ELIMINAR
        //$query_delete = mysqli_query($conexion,"DELETE FROM usuario WHERE id_usuario=$idusuario");
        //SCREAM ESTADO DE REGISTRO
        $query_delete = mysqli_query($conexion,"UPDATE servicio SET servi_estado = 0 WHERE cod_servicio = $idservicio");
        mysqli_close($conexion);
        if($query_delete){
            header("Location: listar_servicio.php");
        }else{
            echo "Error al eliminar el registro.";
        }

    }
    //PARA QUE NO PODAMOS BUSCAR LOS REGISTROS DE ACUERDO A LA URL QUE SE BRINDA
    if(empty($_REQUEST['id']) ){
        header("Location: listar_servicio.php");
       // mysqli_close($conexion);
    }else{
        include "../conexion.php";
        
        $idservicio = $_REQUEST['id'];

        $query = mysqli_query($conexion, "SELECT s.servi_nombre, t.tise_nombre,s.tiempo,s.servi_precio
                                        FROM servicio s INNER JOIN tipo_servicio t ON s.id_tiposer = t.id_tiposer WHERE s.cod_servicio = $idservicio");
        
        mysqli_close($conexion);
        $result = mysqli_num_rows($query);

        if ($result > 0){
            
            while($data = mysqli_fetch_array($query)){

                $nombre = $data['servi_nombre'];
                $tiposer = $data['tise_nombre'];
                $tiempo = $data['tiempo'];
                $precio = $data['servi_precio'];

                
            }
        }else{
            header("Location: listar_servicio.php");
        }
    }


?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Eliminar Servicio</title>
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
            <p>Tipo: <span><?php echo $tiposer; ?></span></p>
            <p>Tiempo (Meses): <span><?php echo $tiempo; ?></span></p>
            <p>Precio: <span><?php echo $precio; ?></span></p>
            <form method="post" action="">
                <input type="hidden" name="idservicio" value="<?php echo $idservicio;?>">
                <a href="listar_servicio.php" class="btn_cancelar"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
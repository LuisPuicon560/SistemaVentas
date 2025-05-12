<?php
//DAR PERMISOS PARA QUE INGRESEN A LAS VISTAS
session_start();

    include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Lista de Usuarios</title>
</head>
<body>
	<?php include "includes/header.php";?>
    <br>  <br>
	<section id="container">

		<h1><i class="fa fa-users"></i> Lista de Usuarios</h1>
        <!--<a href="registro_usuario.php" class="btn_new"><i class="fa fa-user-plus"></i> Crear Usuario</a>-->

        <form action="buscar_usuario.php" method="get" class="form_search">
            <input type="text" name="busquedad" id="busqueda" placeholder="Buscar...">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <table class="table table-light">
            <tbody>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Usuarios</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
                <?php
                //PAGINADOR
                $sql_register = mysqli_query($conexion,"SELECT COUNT(*) AS total_registro FROM usuario WHERE usu_estado = 1");
                $result_register = mysqli_fetch_array($sql_register);
                $total_registro = $result_register['total_registro'];
                //REGISTROS A MOSTRAR POR PAGINA
                $por_pagina  = 5;

                if(empty($_GET['pagina'])){
                    $pagina = 1;
                }else{
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina-1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);

                //LISTAR REGISTROS

                    $query = mysqli_query($conexion,"SELECT u.id_usuario, u.usu_nombre,u.usu_correo,u.usu_usuario, r.rol
                                                    FROM usuario u INNER JOIN rol r ON u.id_rol = r.id_rol WHERE usu_estado = 1 
                                                    ORDER BY u.id_usuario ASC LIMIT $desde,$por_pagina");
                    mysqli_close($conexion);
                    $result = mysqli_num_rows($query);
                    if($result > 0){
                        while($data = mysqli_fetch_array($query)){
                    ?>
                            <tr>
                                <td><?php echo $data["id_usuario"]?></td>
                                <td><?php echo $data["usu_nombre"]?></td>
                                <td><?php echo $data["usu_correo"]?></td>
                                <td><?php echo $data["usu_usuario"]?></td>
                                <td><?php echo $data["rol"]?></td>
                                <td>
                                    <a class="link_edit" href="editar_usuario.php?id=<?php echo $data["id_usuario"]?> "><i class="fas fa-edit"></i>Editar </a>
                                    
                                <?php 
                                   //PARA QUE SOLAMENTE EL ADMIN NO SE ELIMINE SU CUENTA
                                        if($data["id_usuario"] != 1 && $_SESSION['rol'] != 2){
                                    ?>
                                    |
                                    <a class="link_eliminar" href="eliminar_usuario.php?id=<?php echo $data["id_usuario"]?>"><i class="fa fa-trash"></i> Eliminar</a>
                                    <?php
                                }
                            ?>
                                </td>
                            </tr>
                <?php
                        }
                    }

                ?>
            </tbody>
        </table>
        <div class="paginador">
            <ul>
                <?php
                    if($pagina != 1){
                ?>
                <li><a href="?pagina=<?php echo 1;?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina - 1;?>"><i class="fas fa-caret-left fa-lg"></i></a></li>
                <?php
                }
                    for ($i=1; $i <= $total_paginas; $i++){
                        if ($i == $pagina){
                            echo '<li class="pageselect">'.$i.'</li>';
                        }else{
                            echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
                        }
                    }
                    if($pagina != $total_paginas){
                ?>
                <li><a href="?pagina=<?php echo $pagina + 1;?>"><i class="fas fa-caret-right fa-lg"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas?>"><i class="fas fa-step-forward"></i></a></li>
                <?php } ?>
            </ul>
        </div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
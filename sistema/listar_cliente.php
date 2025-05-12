<?php

session_start();

    include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
	<?php include "includes/scripts.php";?>
	<title>LISTA DE CLIENTES | WEBSITE</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <br>
        <br>
		<h1><i class="fa fa-users"></i> Lista de Clientes</h1>
        <!--<a href="registro_cliente.php" class="btn_new"><i class="fa fa-user-plus"></i> Agregar Cliente</a>-->

        <form action="buscar_cliente.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Razon Social...">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>
        <!--******** FORM PARA FILTRAR CON FECHA **********-->
        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_cliente.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" required></input>
                <label>A: </label>
                <input type="date" name="fecha_a" id="fecha_a" required></input>
                <button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
                <!--<div class="btn_reporte" onclick="reporteIngresosPDF();">Reporte</div>-->

            </form>
        </div>


        <table class="table table-light">
            <tbody>
                <tr>
                    <th>ID</th>
                    <th>Fecha de Inicio Contrato</th>
                    <th>Nº Documento</th>
                    <th>Nombre</th>
                    <th>Telefono</th>
                    <th>Direccion</th>
                    <th>Usuario Registrado</th>
                    <th>Acciones</th>
                </tr>
                <?php
                //PAGINADOR
                $sql_register = mysqli_query($conexion,"SELECT COUNT(*) AS total_registro FROM cliente WHERE cli_estado = 1");
                $result_register = mysqli_fetch_array($sql_register);
                $total_registro = $result_register['total_registro'];
                //REGISTROS A MOSTRAR POR PAGINA
                $por_pagina  = 10;

                if(empty($_GET['pagina'])){
                    $pagina = 1;
                }else{
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina-1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);

                //LISTAR REGISTROS

                    $query = mysqli_query($conexion,"SELECT c.id_cliente,c.cli_documento,c.cli_nombre,c.cli_telefono, c.cli_direccion,u.usu_nombre,cli_fechagr FROM cliente c 
                                                    INNER JOIN usuario u ON c.id_usuario = u.id_usuario WHERE cli_estado = 1 
                                                    ORDER BY c.id_cliente DESC LIMIT $desde,$por_pagina");
                    //mysqli_close($conexion);
                    $result = mysqli_num_rows($query);
                    if($result > 0){
                        while($data = mysqli_fetch_array($query)){
                            
                            if($data["cli_documento"] == 0){
                                
                                $documento = 'C/F';
                            }else{
                                $documento = $data["cli_documento"];
                            }
                    ?>
                            <tr>
                                <td><?php echo $data["id_cliente"]?></td>
                                <td><?php echo $data["cli_fechagr"]?></td>
                                <td><?php echo $documento;?></td>
                                <td><?php echo $data["cli_nombre"]?></td>
                                <td><?php echo $data["cli_telefono"]?></td>
                                <td>
                                <div class="div_factura">
                                    <button class="btn_view mostrar_direccion_cliente" fa="<?php echo $data["id_cliente"]; ?>"><i class="fas fa-eye"></i></button>
                                </div>
                            </td>
                            <td><?php echo $data["usu_nombre"]?></td>
                                <td>
                                    <a class="link_edit" href="editar_cliente.php?id=<?php echo $data["id_cliente"]?> "><i class="fas fa-edit"></i>Editar </a>
                                    
                                    <?php
                                    //PARA OCULTAR EL VALOR DE ELIMINAR DEPENDE A LOS ROLES 
                                    if ($_SESSION["rol"] == 1) {
                                    ?>
                                    |
                                    <a class="link_eliminar" href="eliminar_cliente.php?id=<?php echo $data["id_cliente"]?>"><i class="fa fa-trash"></i> Eliminar</a>
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
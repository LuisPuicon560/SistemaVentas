<?php
//DAR PERMISOS PARA QUE INGRESEN A LAS VISTAS
session_start();
    include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png"> 
	<?php include "includes/scripts.php";?>
	<title>Lista de Servicios</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <?php

            $busquedad  = strtolower($_REQUEST['busquedad']);
            
            if(empty($busquedad)){
                
                header("Location: listar_servicio.php");
                mysqli_close($conexion);
            }

        ?>
		<h1>Lista de Servicios</h1>
        

        <form action="buscar_servicio.php" method="get" class="form_search">
            <input type="text" name="busquedad" id="busqueda" placeholder="Buscar..." value="<?php echo $busquedad; ?>">
            <a href="listar_servicio.php" class="btn_cl"> X</a>
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <table class="table table-light">
            <tbody>
                <tr>
                    <th>CODIGO</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Tiempo (Meses)</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
                <?php
                //PAGINADOR
                $rol= '';
                //PARA BUSCAR - SE COLOCA LOS NOMBRES DE LOS ROLES
                if ($busquedad == 'planes de admin. redes sociales'){

                    $rol = " OR id_tiposer LIKE '%1%' ";
                }else if($busquedad == 'pack flyers'){
                    $rol = " OR id_tiposer LIKE '%2%' ";
                }else if($busquedad == 'nuvos planes full connectd'){
                    $rol = " OR id_tiposer LIKE '%3%' ";
                }else if($busquedad == 'hosting linux'){
                    $rol = " OR id_tiposer LIKE '%4%' ";
                }else if($busquedad == 'dominios'){
                    $rol = " OR id_tiposer LIKE '%5%' ";
                }else if($busquedad == 'planes de paginas web'){
                    $rol = " OR id_tiposer LIKE '%6%' ";
                }else if($busquedad == 'videos de contenidos'){
                    $rol = " OR id_tiposer LIKE '%7%' ";
                }else if($busquedad == 'diseño grafico'){
                    $rol = " OR id_tiposer LIKE '%8%' ";
                }else if($busquedad == 'branding y re-branding'){
                    $rol = " OR id_tiposer LIKE '%9%' ";
                }else if($busquedad == 'chat bots'){
                    $rol = " OR id_tiposer LIKE '%10%' ";
                }else if($busquedad == 'servicios profesionales'){
                    $rol = " OR id_tiposer LIKE '%11%' ";
                }else if($busquedad == 'sesion fotograficas para contenidos'){
                    $rol = " OR id_tiposer LIKE '%12%' ";
                }
                $sql_register = mysqli_query($conexion,"SELECT COUNT(*) AS total_registro FROM servicio WHERE 
                                                        ( cod_servicio LIKE '%$busquedad%'OR 
                                                        servi_nombre LIKE '%$busquedad%' OR 
                                                        tiempo LIKE '%$busquedad%' 
                                                        $rol ) AND servi_estado = 1");
                
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

                    $query = mysqli_query($conexion,"SELECT s.cod_servicio, s.servi_nombre,t.tise_nombre,s.tiempo,s.servi_precio
                                                    FROM servicio s INNER JOIN tipo_servicio t ON s.id_tiposer = t.id_tiposer WHERE 
                                                    (cod_servicio LIKE '%$busquedad%'OR 
                                                    servi_nombre LIKE '%$busquedad%' OR 
                                                    t.tise_nombre LIKE '%$busquedad%' OR 
                                                    tiempo LIKE '%$busquedad%') AND
                                                    servi_estado = 1 ORDER BY s.cod_servicio ASC LIMIT $desde,$por_pagina");
                    mysqli_close($conexion);
                    $result = mysqli_num_rows($query);
                    if($result > 0){
                        while($data = mysqli_fetch_array($query)){
                    ?>
                            <tr>
                                <td><?php echo $data["cod_servicio"]?></td>
                                <td><?php echo $data["servi_nombre"]?></td>
                                <td><?php echo $data["tise_nombre"]?></td>
                                <td><?php echo $data["tiempo"]?></td>
                                <td><?php echo $data["servi_precio"]?></td>
                                <td>
                                    <a class="link_edit" href="editar_servicio.php ? id=<?php echo $data["cod_servicio"]?> ">Editar </a>
                                    |
                                    <a class="link_eliminar" href="eliminar_servicio.php ? id=<?php echo $data["cod_servicio"]?>"> Eliminar</a>
                                </td>
                            </tr>
                <?php
                        }
                    }

                ?>
            </tbody>
        </table>
        <?php
            if($total_registro !=0){
        ?>
        <div class="paginador">
            <ul>
                <?php
                    if($pagina != 1){
                ?>
                <li><a href="?pagina=<?php echo 1; ?>&busquedad=<?php echo $busquedad;?>"> |<</a></li>
                <li><a href="?pagina=<?php echo $pagina - 1; ?>&busquedad=<?php echo $busquedad;?>?>"> <<</a></li>
                <?php
                }
                    for ($i=1; $i <= $total_paginas; $i++){
                        if ($i == $pagina){
                            echo '<li class="pageselect">'.$i.'</li>';
                        }else{
                            echo '<li><a href="?pagina='.$i.'&busquedad='.$busquedad.'">'.$i.'</a></li>';
                        }
                    }
                    if($pagina != $total_paginas){
                ?>
                <li><a href="?pagina=<?php echo $pagina + 1;?>&busquedad=<?php echo $busquedad;?>"> >></a></li>
                <li><a href="?pagina=<?php echo $total_paginas?>&busquedad=<?php echo $busquedad;?>"> >|</a></li>
                <?php } ?>
            </ul>
        </div>
        <?php
        }
        ?>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
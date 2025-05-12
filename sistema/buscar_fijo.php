<?php

session_start();

    include "../conexion.php";
    $busqueda = '';
    $fecha_de = '';
    $fecha_a = '';
    
    if( isset($_REQUEST['busqueda']) && $_REQUEST['busqueda'] == '' ){
        header("location:listar_egreso_fijo.php");
    }
    if( isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])){
        if($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == ''){
            header("location:listar_egreso_fijo.php");
        }
    }
    //VALIDAMOS CON EL DE BUSQUEDAD
    if(!empty($_REQUEST['busqueda'])){
        if(!is_numeric($_REQUEST['busqueda'])){
            header("location: listar_egreso_fijo.php");
        }
        $busqueda = strtolower($_REQUEST['busqueda']);
        $where = "id_fijo = $busqueda";
        $buscar = "busqueda = $busqueda";
    }

    //VALIDAMOS CON LO QUE SON LAS FECHAS 
    if(!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])){
        $fecha_de = $_REQUEST['fecha_de'];
        $fecha_a = $_REQUEST['fecha_a'];

        $buscar = '';

        if($fecha_de > $fecha_a){
            header("location: listar_egreso_fijo.php");
        }else if($fecha_de == $fecha_a){
            $where = "fj_fecha LIKE '$fecha_de%'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
        }else{
            $f_de = $fecha_de.' 00:00:00';
            $f_a = $fecha_a.' 23:59:59';
            $where = "fj_fecha BETWEEN '$f_de' AND '$f_a'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>LISTA DE EGRESOS FIJOS | WEBSITE</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">

		<h1><i class="far fa-newspaper"></i> Lista de Egresos de Costo Fijo</h1>
        <!--<a href="registro_cliente.php" class="btn_new"><i class="fa fa-user-plus"></i> Agregar Cliente</a>-->

        <form action="buscar_fijo.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nº de Egreso....." value="<?php echo $busqueda;?>">
            <a href="listar_egreso_fijo.php" class="btn_cl"> X</a>
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <!--******** FORM PARA FILTRAR CON FECHA **********-->
        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_fijo.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de;?>" required></input>
                <label>A: </label>
                <input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a;?>" required></input>
                <button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <table class="table table-light">
            <tbody>
                <tr>
                    <th>Nº</th>
                    <th>Fecha / Hora</th>
                    <th>Servicio</th>
                    <th>Empresa</th>
                    <th>Detalle</th>
                    <th class="textright">Total De Egreso</th>
                </tr>
                <?php
                //PAGINADOR
                $sql_register = mysqli_query($conexion,"SELECT COUNT(*) AS total_registro FROM egreso_fijo WHERE $where ");
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

                    $query = mysqli_query($conexion,"SELECT e.id_fijo,e.fj_servicio,e.fj_empresa,e.fj_descripcion,e.fj_monto,e.fj_fecha  FROM egreso_fijo e 
                                                    WHERE $where AND e.fj_estado != 10 ORDER BY e.fj_fecha DESC LIMIT $desde,$por_pagina");
                    //mysqli_close($conexion);
                    $result = mysqli_num_rows($query);
                    if($result > 0){
                        while($data = mysqli_fetch_array($query)){
                    ?>
                            <tr id="row_<?php echo $data["id_fijo"]; ?>">
                            <td><?php echo $data["id_fijo"]; ?></td>
                            <td><?php echo $data["fj_fecha"]; ?></td>
                            <td><?php echo $data["fj_servicio"]; ?></td>
                            <td><?php echo $data["fj_empresa"]; ?></td>
                            <td>
                                <div class="div_factura">
                                    <button class="btn_view mostrar_detalle" fa="<?php echo $data["id_fijo"]; ?>"><i class="fas fa-eye"></i></button>
                                </div>
                            </td>
                            <td class="textright totalfactura"><span>S/.</span><?php echo $data["fj_monto"]; ?></td>
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
                <li><a href="?pagina=<?php echo 1;?>&<?php echo $buscar;?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina - 1;?>&<?php echo $buscar;?>"><i class="fas fa-caret-left fa-lg"></i></a></li>
                <?php
                }
                    for ($i=1; $i <= $total_paginas; $i++){
                        if ($i == $pagina){
                            echo '<li class="pageselect">'.$i.'</li>';
                        }else{
                            echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';
                        }
                    }
                    if($pagina != $total_paginas){
                ?>
                <li><a href="?pagina=<?php echo $pagina + 1;?>&<?php echo $buscar;?>"><i class="fas fa-caret-right fa-lg"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas?>&<?php echo $buscar;?>"><i class="fas fa-step-forward"></i></a></li>
                <?php } ?>
            </ul>
        </div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>
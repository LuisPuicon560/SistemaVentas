<?php

session_start();

    include "../conexion.php";
    $busqueda = '';
    $fecha_de = '';
    $fecha_a = '';
    
    if( isset($_REQUEST['busqueda']) && $_REQUEST['busqueda'] == '' ){
        header("location:ventas.php");
    }
    if( isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])){
        if($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == ''){
            header("location:ventas.php");
        }
    }
    //VALIDAMOS CON EL DE BUSQUEDAD
    if(!empty($_REQUEST['busqueda'])){
        if(!is_numeric($_REQUEST['busqueda'])){
            header("location:ventas.php");
        }
        $busqueda = strtolower($_REQUEST['busqueda']);
        $where = "id_comprobante = $busqueda";
        $buscar = "busqueda = $busqueda";
    }

    //VALIDAMOS CON LO QUE SON LAS FECHAS 
    if(!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])){
        $fecha_de = $_REQUEST['fecha_de'];
        $fecha_a = $_REQUEST['fecha_a'];

        $buscar = '';

        if($fecha_de > $fecha_a){
            header("location: ventas.php");
        }else if($fecha_de == $fecha_a){
            $where = "com_fechaemi LIKE '$fecha_de%'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
        }else{
            $f_de = $fecha_de.' 00:00:00';
            $f_a = $fecha_a.' 23:59:59';
            $where = "com_fechaemi BETWEEN '$f_de' AND '$f_a'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>LISTA DE INGRESOS | WEBSITE</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
        <br><br>
		<h1><i class="far fa-newspaper"></i> Lista de Ingresos</h1>
        <!--<a href="registro_cliente.php" class="btn_new"><i class="fa fa-user-plus"></i> Agregar Cliente</a>-->

        <form action="buscar_venta.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nº de Factura....." value="<?php echo $busqueda;?>">
            <a href="ventas.php" class="btn_cl"> X</a>
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <!--******** FORM PARA FILTRAR CON FECHA **********-->
        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_venta.php" method="get" class="form_search_date">
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
                    <th>Cliente</th>
                    <th>Vendedor </th>
                    <th>Estado</th>
                    <th class="textright">Total Factura</th>
                    <th class="textright">Acciones</th>
                </tr>
                <?php
                //PAGINADOR
                $sql_register = mysqli_query($conexion,"SELECT COUNT(*) AS total_registro FROM comprobante WHERE $where ");
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

                    $query = mysqli_query($conexion,"SELECT f.id_comprobante,f.com_fechaemi,f.com_totalfactura,f.id_cliente,f.com_estado,u.usu_nombre AS vendedor,
                                                    cl.cli_nombre AS cliente FROM comprobante f 
                                                    INNER JOIN usuario u ON f.id_usuario = u.id_usuario INNER JOIN cliente cl ON f.id_cliente = cl.id_cliente 
                                                    WHERE $where AND f.com_estado != 10 ORDER BY f.com_fechaemi DESC LIMIT $desde,$por_pagina");
                    //mysqli_close($conexion);
                    $result = mysqli_num_rows($query);
                    if($result > 0){
                        while($data = mysqli_fetch_array($query)){
                            if($data["com_estado"] == 1){
                                $estado = '<span class="pagada">Pagada</span>';
                            }else{
                                $estado = '<span class="anulada">Anulada</span>';
                            }
                    ?>
                            <tr id="row_<?php echo $data["id_comprobante"];?>">
                                <td><?php echo $data["id_comprobante"];?></td>
                                <td><?php echo $data["com_fechaemi"];?></td>
                                <td><?php echo $data["cliente"];?></td>
                                <td><?php echo $data["vendedor"];?></td>
                                <td><?php echo $estado;?></td>
                                <td class="textright totalfactura"><span>S/.</span><?php echo $data["com_totalfactura"];?></td>

                                <td>
                                    <div class="div_acciones">
                                        <div>
                                            <button class="btn_view view_factura" type="button" cl="<?php echo $data["id_cliente"];?>" f="<?php echo $data["id_comprobante"];?>">
                                            <i class="fas fa-eye"></i></button>
                                        </div>

                                    <!--ESTE DIV SOLO SE VA A MOSTRAR PARA ADMINISTRADORES Y SUPERVISORES-->

                                    <?php 
                                        if ($_SESSION['rol'] == 1 ||  $_SESSION['rol'] == 2 || $_SESSION['rol'] == 4){
                                            if($data['com_estado'] == 1){
                                    ?>
                                    <div class="div_factura">
                                        <button class="btn_anular anular_factura" fa="<?php echo $data["id_comprobante"];?>"><i class="fas fa-ban"></i></button>
                                    </div>
                                    <?php 
                                            }else{ ?>
                                            <div class="div_factura">
                                            <button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>
                                            </div>
                                    <?php
                                            }
                                        }
                                    ?>
                                    </div>
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
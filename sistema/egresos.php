<?php

session_start();

include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <title>LISTA DE EGRESOS | WEBSITE</title>

</head>

<body>
    
    <?php include "includes/header.php"; ?>
    <br>
    <section id="container">
        <br>
        <h1><i class="far fa-newspaper"></i> Lista de Egresos</h1>
        <!--<a href="registro_cliente.php" class="btn_new"><i class="fa fa-user-plus"></i> Agregar Cliente</a>-->

        <form action="buscar_egreso.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nº de Egreso.....">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <!--******** FORM PARA FILTRAR CON FECHA **********-->
        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_egreso.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" required></input>
                <label>A: </label>
                <input type="date" name="fecha_a" id="fecha_a" required></input>
                <button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
                <div class="btn_reporte" onclick="reporteEgresosPDF();">Reporte</div>
            </form>
        </div>

        <table class="table table-light">
            <tbody>
                <tr>
                    <th>Nº</th>
                    <th>Fecha / Hora</th>
                    <th>Nº Documento</th>
                    <th>Razon Social</th>
                    <th>Telefono</th>
                    <th>Direccion</th>
                    <th>Ciudad</th>
                    <th>Departamento</th>
                    <th>Subtotal</th>
                    <th>IGV</th>
                    <th>Detalle</th>
                    <th class="textright">Total Factura</th>
                </tr>
                <?php
                //PAGINADOR
                $sql_registro = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM ext WHERE estado != 10");
                $result_registro = mysqli_fetch_array($sql_registro);
                $total_registro = $result_registro['total_registro'];
                //REGISTROS A MOSTRAR POR PAGINA
                $por_pagina  = 5;

                if (empty($_GET['pagina'])) {
                    $pagina = 1;
                } else {
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina - 1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);

                //LISTAR REGISTROS

                $query = mysqli_query($conexion, "SELECT e.id_externo, e.n_documento, e.razon_social, e.telefono, e.ciudad, e.departamento,
                                                    e.direccion, e.subtotal, e.igv, e.total, e.detalle, e.fechaemi FROM ext e
                                                    WHERE e.estado != 10 ORDER BY e.fechaemi DESC LIMIT $desde,$por_pagina");
                //mysqli_close($conexion);
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_array($query)) {
                ?>
                        <tr id="row_<?php echo $data["id_externo"]; ?>">
                            <td><?php echo $data["id_externo"]; ?></td>
                            <td><?php echo $data["fechaemi"]; ?></td>
                            <td><?php echo $data["n_documento"]; ?></td>
                            <td><?php echo $data["razon_social"]; ?></td>
                            <td><?php echo $data["telefono"]; ?></td>
                            <td>
                                <div class="div_factura">
                                    <button class="btn_view mostrar_direccion" fa="<?php echo $data["id_externo"]; ?>"><i class="fas fa-eye"></i></button>
                                </div>
                            </td>
                            <td><?php echo $data["ciudad"]; ?></td>
                            <td><?php echo $data["departamento"]; ?></td>
                            <td><?php echo $data["subtotal"]; ?></td>
                            <td><?php echo $data["igv"]; ?></td>
                            <td>
                                <div class="div_factura">
                                    <button class="btn_view mostrar_detalle" fa="<?php echo $data["id_externo"]; ?>"><i class="fas fa-eye"></i></button>
                                </div>
                            </td>
                            <td class="textright totalfactura"><span>S/.</span><?php echo $data["total"]; ?></td>
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
                if ($pagina != 1) {
                ?>
                    <li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-step-backward"></i></a></li>
                    <li><a href="?pagina=<?php echo $pagina - 1; ?>"><i class="fas fa-caret-left fa-lg"></i></a></li>
                <?php
                }
                for ($i = 1; $i <= $total_paginas; $i++) {
                    if ($i == $pagina) {
                        echo '<li class="pageselect">' . $i . '</li>';
                    } else {
                        echo '<li><a href="?pagina=' . $i . '">' . $i . '</a></li>';
                    }
                }
                if ($pagina != $total_paginas) {
                ?>
                    <li><a href="?pagina=<?php echo $pagina + 1; ?>"><i class="fas fa-caret-right fa-lg"></i></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas ?>"><i class="fas fa-step-forward"></i></a></li>
                <?php } ?>
            </ul>
        </div><br>

    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>
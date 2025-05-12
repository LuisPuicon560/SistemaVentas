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
    <title>LISTA DE EGRESOS PERSONAL | WEBSITE</title>

</head>

<body>
    
    <?php include "includes/header.php"; ?>
    <br>
    <section id="container">
        <br>
        <h1><i class="far fa-newspaper"></i> Lista de Egresos Personal</h1>
        <!--<a href="registro_cliente.php" class="btn_new"><i class="fa fa-user-plus"></i> Agregar Cliente</a>-->

        <form action="buscar_personal.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nº de Egreso.....">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <!--******** FORM PARA FILTRAR CON FECHA **********-->
        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_personal.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" required></input>
                <label>A: </label>
                <input type="date" name="fecha_a" id="fecha_a" required></input>
                <button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
                <div class="btn_reporte" onclick="reporteEgresosPerPDF();">Reporte</div>
            </form>
        </div>

        <table class="table table-light">
            <tbody>
                <tr>
                    <th>Nº</th>
                    <th>Fecha / Hora</th>
                    <th>Nº Documento</th>
                    <th>Nombre del Personal</th>
                    <th>Cargo</th>
                    <th class="textright">Total Egreso</th>
                </tr>
                <?php
                //PAGINADOR
                $sql_registro = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM egreso_personal WHERE estado != 10");
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

                $query = mysqli_query($conexion, "SELECT e.id_egpe, e.ep_nombre,e.ep_ndocumento,e.ep_cargo,e.ep_total,e.ep_fecha FROM egreso_personal e
                                                    WHERE e.estado != 10 ORDER BY e.ep_fecha DESC LIMIT $desde,$por_pagina");
                //mysqli_close($conexion);
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_array($query)) {
                ?>
                        <tr id="row_<?php echo $data["id_egpe"]; ?>">
                            <td><?php echo $data["id_egpe"]; ?></td>
                            <td><?php echo $data["ep_fecha"]; ?></td>
                            <td><?php echo $data["ep_ndocumento"]; ?></td>
                            <td><?php echo $data["ep_nombre"]; ?></td>
                            <td><?php echo $data["ep_cargo"]; ?></td>
                            <td class="textright totalfactura"><span>S/.</span><?php echo $data["ep_total"]; ?></td>
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
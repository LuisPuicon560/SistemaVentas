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
    <?php include "includes/scripts.php"; ?>
    <title>Lista de Servicios</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <br>
    <section id="container">
        <br>
        <h1><i class="fa fa-users"></i> Lista de Servicios</h1>
        <!--<a href="registro_usuario.php" class="btn_new"><i class="fa fa-user-plus"></i> Crear Usuario</a>-->

        <form action="buscar_servicio.php" method="get" class="form_search">
            <input type="text" name="busquedad" id="busqueda" placeholder="Buscar...">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <table class="table table-light">
            <tbody>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Contrato (Meses)</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
                <?php
                //PAGINADOR
                $sql_register = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM servicio WHERE servi_estado = 1");
                $result_register = mysqli_fetch_array($sql_register);
                $total_registro = $result_register['total_registro'];
                //REGISTROS A MOSTRAR POR PAGINA
                $por_pagina  = 10;

                if (empty($_GET['pagina'])) {
                    $pagina = 1;
                } else {
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina - 1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);

                //LISTAR REGISTROS

                $query = mysqli_query($conexion, "SELECT s.cod_servicio, s.servi_nombre,s.tiempo,t.tise_nombre,s.servi_precio
                                                    FROM servicio s INNER JOIN tipo_servicio t ON s.id_tiposer = t.id_tiposer WHERE servi_estado = 1 
                                                    ORDER BY s.cod_servicio ASC LIMIT $desde,$por_pagina");
                mysqli_close($conexion);
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_array($query)) {
                ?>
                        <tr>
                            <td><?php echo $data["cod_servicio"] ?></td>
                            <td><?php echo $data["servi_nombre"] ?></td>
                            <td><?php echo $data["tise_nombre"] ?></td>
                            <td><?php echo $data["tiempo"] ?></td>
                            <td><?php echo $data["servi_precio"] ?></td>
                            <td>
                                <a class="link_edit" href="editar_servicio.php ? id=<?php echo $data["cod_servicio"] ?> "><i class="fas fa-edit"></i> Editar </a>
                                |
                                <a class="link_eliminar" href="eliminar_servicio.php ? id=<?php echo $data["cod_servicio"] ?>"><i class="fa fa-trash"></i> Eliminar</a>
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
        </div>

    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>
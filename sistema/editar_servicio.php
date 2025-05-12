<?php
//DAR PERMISOS PARA QUE INGRESEN A LAS VISTAS
session_start();

include "../conexion.php";
if (!empty($_POST)) {
    $alert = '';
    if (empty($_POST['nombre']) || empty($_POST['tiempo']) || empty($_POST['tiposer']) || empty($_POST['precio'])) {

        $alert = '<p class="msg_error"> Todos los campos son obligatorios.</p>';
    } else {

        $idservicio = $_POST['idservicio'];
        $nombre = $_POST['nombre'];
        $tiposer = $_POST['tiposer'];
        $tiempo = $_POST['tiempo'];
        $precio = $_POST['precio'];

        //PARA QUE NO SE REPITAN
        $query = mysqli_query($conexion, "SELECT * FROM servicio WHERE (servi_nombre = '$nombre' AND cod_servicio = '$idservicio')");

        $result = mysqli_fetch_array($query);
        //$result = count($result);
        if ($result > 0) {
            $alert = '<p class="msg_error"> El servicio ya existe, opta por otro</p>';
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE servicio SET 
                id_tiposer = '$tiposer',
                tiempo = '$tiempo',
                servi_nombre = '$nombre',
                servi_precio = '$precio'
                WHERE cod_servicio = $idservicio ");

            if ($sql_update) {

                $alert = '<p class="msg_save">Servicio actualizado correctamente.</p>';
            } else {

                $alert = '<p class="msg_error"> Error al actualizar el servicio, intentelo de nuevo.</p>';
            }
        }
    }
}
//MOSTRAR DATOS
if (empty($_REQUEST['id'])) {
    header('Location: listar_servicio.php ');
}
$idservicio = $_REQUEST['id'];

$sql = mysqli_query($conexion, "SELECT s.cod_servicio, s.servi_nombre, (s.id_tiposer) AS idtiposer,(t.tise_nombre) AS nombretiposer,s.tiempo,s.servi_precio FROM servicio s INNER JOIN tipo_servicio t ON s.id_tiposer=t.id_tiposer
                                    WHERE cod_servicio= $idservicio and servi_estado = 1");



$result_sql = mysqli_num_rows($sql);
if ($result_sql == 0) {
    header("Location: listar_servicio.php");
} else {
    $option = '';
    while ($data = mysqli_fetch_array($sql)) {

        $idservicio = $data['cod_servicio'];
        $tiposer = $data['idtiposer'];
        $nombre = $data['servi_nombre'];
        $tiempo = $data['tiempo'];
        $precio = $data['servi_precio'];
        $tipo = $data['nombretiposer'];
    }
    //PARA QUE NOS MUESTRE DEAFULT EL ROL (SI HAY UN ROL MAS, SE LE AGREGA UN ELSE IF MAS)
    if ($tiposer == 1) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 2) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 3) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 4) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 5) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 6) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 7) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 8) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 9) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 10) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 11) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    } else if ($tiposer == 12) {
        $option = '<option value="' . $tiposer . '"select>' . $tipo . '</option>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <title>ACTUALIZAR SERVICIO | WEBSITE</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1>Editar Servicio</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
            <form action="" method="post">
                <input type="hidden" name="idservicio" value="<?php echo $idservicio; ?>">
                <label for="nombre">Nombre Servicio:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Numero de Documento" value="<?php echo $nombre; ?>">
                <label for="tiposer"> Tipo de Servicio:</label>
                <?php
                include "../conexion.php";
                $query_tipodoc = mysqli_query($conexion, "SELECT * FROM tipo_servicio");

                $result_tipodoc  = mysqli_num_rows($query_tipodoc);

                ?>

                <select name="tiposer" id="tiposer" class="notItemOne">
                    <?php
                    echo $option;
                    if ($result_tipodoc > 0) {

                        while ($tipo =  mysqli_fetch_array($query_tipodoc)) {
                    ?>
                            <option value="<?php echo $tipo['id_tiposer'] ?>"><?php echo $tipo['tise_nombre'] ?></option>

                    <?php
                        }
                    }
                    ?>
                </select>
                <label for="tiempo">Tiempo (Meses):</label>
                <input type="number" name="tiempo" id="tiempo" placeholder="Cantidad de Meses" value="<?php echo $tiempo; ?>">
                <label for="precio">Precio:</label>
                <input type="number" name="precio" id="precio" placeholder="Nombre Cliente" value="<?php echo $precio; ?>">



                <button type="submit" class="btn_save"><i class="far fa-save"></i> Modificar Servicio</button>
                <a href="listar_servicio.php" class="btn_atras"><i class="fa fa-backward"></i> Atras</a>
            </form>
        </div>

    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>
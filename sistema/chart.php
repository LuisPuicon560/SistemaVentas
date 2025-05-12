<?php
include("../conexion.php");
if ($_POST['action'] == 'polarChart') {
    $arreglo = array();
    $query = mysqli_query($conexion, "SELECT i.cod_servicio, i.servi_nombre, i.tiempo, SUM(i.cod_servicio) as total FROM servicio i group by i.cod_servicio ORDER BY i.cod_servicio DESC");
    while ($data = mysqli_fetch_array($query)) {
        $arreglo[] = $data;
    }
    echo json_encode($arreglo);
    die();
}

?>
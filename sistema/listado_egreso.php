<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
	<title>Sistema Ventas</title>
</head>

<body>
    <?php include "includes/header.php"; ?>

    <div class="container">
        <div class="card">
            <img src="img/tabla.png">
            <h4>LISTADO DE EGRESOS</h4>
            <a href="egresos.php"> VER </a>
        </div>
        <div class="card">
            <img src="img/tabla.png">
            <h4>LISTADO DE EGRESO DEL PERSONAL</h4>
            <a href="lista_egresos_personal.php"> VER </a>
        </div>
        <div class="card">
            <img src="img/tabla.png">
            <h4>LISTADO DE EGRESO DE COSTO FIJO</h4>
            <a href="listar_egreso_fijo.php"> VER </a>
        </div>
        <div class="card">
            <img src="img/tabla.png">
            <h4>LISTADO EGRESO DE COSTO VARIABLE</h4>
            <a href="listar_egreso_variable.php"> VER </a>
        </div>
    </div>
	
    <?php include "includes/footer.php"; ?>
</body>

</html>
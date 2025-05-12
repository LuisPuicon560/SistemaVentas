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
            <img src="img/egreso.png">
            <h4>NUEVO EGRESO</h4>
            <a href="nuevo_egreso.php"> EMITIR </a>
        </div>
        <div class="card">
            <img src="img/empleador.png">
            <h4>NUEVO EGRESO DE PERSONAL</h4>
            <a href="egreso_personal.php"> EMITIR </a>
        </div>
        <div class="card">
            <img src="img/fijo.png">
            <h4>NUEVO EGRESO DE COSTO FIJO</h4>
            <a href="egreso_fijo.php"> EMITIR </a>
        </div>
     
        <div class="card">
            <img src="img/variable.png">
            <h4>NUEVO EGRESO DE COSTO VARIABLE</h4>
            <a href="egreso_variable.php"> EMITIR </a> 
        </div> 
       
    </div> 
    <?php include "includes/footer.php"; ?>
</body>

</html>
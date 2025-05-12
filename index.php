<?php
$alert = '';
session_start();
//VALIDAR SI HAY LA VARIABLE DE SESION PARA NO REGRESAR CUANOD YA HEMOS INGRESADO AL MENU
if (!empty($_SESSION['active'])) {
    header('location: sistema/');
} else {
    //VALIDAR SI ESTA INGRESADA LA CLAVE 
    if (!empty($_POST)) {

        if (empty($_POST['usuario']) || empty($_POST['clave'])) {
            $alert = 'Ingrese su usuario y su clave';
        } else {

            require_once "conexion.php";
            //QUITA LOS CARACTERES RAROS (PARA QUE NO HAKEEN)
            $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
            // EL MD5 SIRVE PARA ENCRIPTAR
            $pass = md5(mysqli_real_escape_string($conexion, $_POST['clave']));

            $query = mysqli_query($conexion, "SELECT u.id_usuario,u.usu_nombre,u.usu_correo,u.usu_usuario,r.id_rol,r.rol
                                                FROM usuario u INNER JOIN rol r ON u.id_rol = r.id_rol WHERE usu_usuario='$user' AND usu_clave='$pass'");
            mysqli_close($conexion);
            $result = mysqli_num_rows($query);

            if ($result > 0) {
                $data = mysqli_fetch_array($query);
                $_SESSION['active'] = true;
                $_SESSION['usuario_id'] = $data['id_usuario'];
                $_SESSION['nombre'] = $data['usu_nombre'];
                $_SESSION['email'] = $data['usu_correo'];
                $_SESSION['user'] = $data['usu_usuario'];
                $_SESSION['rol'] = $data['id_rol'];
                $_SESSION['nomrol'] = $data['rol'];

                header('location: sistema/');
            } else {
                $alert = 'El usuario o la clave son incorrectos';
                session_destroy();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es" class=" ">

<head>
    <title>WEBSITE MKTD</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charSet="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="sistema/img/logoempresa.png">
</head>

<body>
    <img class="wave" src="img/wave.png">
    <div class="container">
        <div class="img">
            <img src="img/LOGOWB.png">
        </div>
        <div class="login-content">
            <form action="" method="post">
                <h2 class="title">Bienvenido</h2>
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <input type="text" name="usuario" class="input" placeholder="Usuario">
                    </div>
                </div>
                <div class="input-div pass">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <input type="password" name="clave" class="input" placeholder="Contraseña">
                    </div>
                </div>
                <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
                <input type="submit" value="INGRESAR" class="btn">
            </form>
        </div>
    </div>
    <script type="text/javascript" src="js/functions.js"></script>
</body>

</html>
<?php

    session_start();
   date_default_timezone_set("America/Lima");

    include "../conexion.php";
    //print md5($_SESSION['usuario_id']);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
    <?php include "includes/scripts.php";?>
    <title>NUEVA EGRESO | WEBSITE</title>
</head>
<body>
    <?php include "includes/header.php";?>
    <section id="container">
        <div class="title_page">
            <h1><i class="fas fa-cube"></i> NUEVO EGRESO</h1>
        </div>
        <br>
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4> Datos del Egreso</h4>
            </div>
            <form method="post" class="datos" id="formulario" action="">
            
                <div class="wd30">
                    <label>Nº de Documento:</label>
                    <input type="text" name="documento_usuario" id="documento_usuario" required>
                </div>
                <div class="wd30">
                    <label>Razon Social:</label>
                    <input type="text" name="nom_usuario" id="nom_usuario" required>
                </div>
                <div class="wd30">
                    <label>Telefono:</label>
                    <input type="number" name="tel_usuario" id="tel_usuario" required>
                </div>
                 <div class="wd30">
                    <label>Ciudad:</label>
                    <input type="text" name="ciudad_usuario" id="ciudad_usuario" required>
                </div>
                <div class="wd30">
                    <label>Departamento:</label>
                    <input type="text" name="depa_usuario" id="depa_usuario" required>
                </div>
                <div class="wd301">
                    <label>Direccion:</label>
                    <input type="text" name="dire_usuario" id="dire_usuario" required>
                </div>
                <div class="wd30">
                    <label>Subtotal:</label>
                    <input type="text" name="subtotal" id="subtotal" required>
                </div>
                <div class="wd30">
                    <label>IGV:</label>
                    <input type="text" name="igv" id="igv" required>
                </div>
                <div class="wd30">
                    <label>Total del Egreso:</label>
                    <input type="text" name="total" id="total"required>
                </div>
                <div class="wd302">
                    <label>Detalle Egreso:</label>
                    <textarea  type="text" name="detalle" id="detalle" cols="115" rows="5"  required></textarea><br></br>
                    <input type="submit" value="Enviar" name="registrar">
                </div>
                <?php

                    if(!empty($_POST["registrar"])){
                        if(empty($_POST["documento_usuario"]) OR empty($_POST["nom_usuario"]) OR empty($_POST["tel_usuario"]) 
                            OR empty($_POST["ciudad_usuario"]) OR empty($_POST["depa_usuario"]) OR empty($_POST["dire_usuario"]) OR 
                            empty($_POST["subtotal"]) OR empty($_POST["igv"]) OR empty($_POST["total"]) OR empty($_POST["detalle"])){
        
                            $alert = '<p class="msg_error"> Falta llenar campos.</p>';
                        }else{
                            $n_documento = $_POST["documento_usuario"];
                            $nombre = $_POST["nom_usuario"];
                            $telefono = $_POST["tel_usuario"];
                            $ciudad = $_POST["ciudad_usuario"];
                            $departamento = $_POST["depa_usuario"];
                            $direccion = $_POST["dire_usuario"];
                            $subtotal = $_POST["subtotal"];
                            $igv = $_POST["igv"];
                            $total = $_POST["total"];
                            $detalle = $_POST["detalle"];
                            $sql = $conexion -> query("INSERT INTO ext (n_documento,razon_social,telefono,ciudad,departamento,direccion,subtotal,igv,total,detalle)
                                                        VALUES ('$n_documento','$nombre','$telefono','$ciudad','$departamento','$direccion','$subtotal','$igv','$total','$detalle')");

                            if($sql==1){
                                $alert = '<p class="msg_save">Egreso guardado correctamente.</p>';

                            }else{
                                $alert = '<p class="msg_error"> Error al registrar, intentelo de nuevo.</p>';  
                            }
                        }
                    }
                ?>
            </form>
        </div>
        
    </section>

    <!--********* PARA CUANDO NOS MOVAMOS A OTRA PAGINA Y TENGAMOS QUE BUSCAR OTRA COSA, NO SE BORREN LOS DATOS DE LA NUEVA VENTA-->
    <script type="text/javascript">

        $(document).ready(function(){
            var usuarioid = '<?php echo  $_SESSION['usuario_id'];?>';
            serchForDetalles(usuarioid);
        });

    </script>

    
</body>
</html>
<?php

    session_start();
    include "../conexion.php";
    //print md5($_SESSION['usuario_id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
    <?php include "includes/scripts.php";?>
    <title>NUEVA EGRESO COSTO FIJO | WEBSITE</title>
</head>
<body>
    <?php include "includes/header.php";?>
    <section id="container">
        <div class="title_page"><br><br>
            <h1><i class="fas fa-cube"></i> NUEVO EGRESO COSTO FIJO</h1>
        </div>
        <br>
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4> Datos del Egreso de Costo Fijo</h4>
            </div>
            <form method="post" class="datos" id="formulario" action="">
            
                <div class="wd30">
                    <label>Servicio:</label>
                    <input type="text" name="servicio" id="servicio" required><br>
                </div>
                <div class="wd30">
                    <label>Empresa:</label>
                    <input type="text" name="empresa" id="empresa" required>
                </div>
                <div class="wd30">
                    <label>Total del Monto:</label>
                    <input type="text" name="total" id="total"required>
                </div>
                <div class="wd302">
                    <label>Descripcion:</label>
                    <textarea  type="text" name="detalle" id="detalle" cols="115" rows="5"  required></textarea><br></br>
                    <input type="submit" value="Enviar" name="registrar">
                </div>
                <?php

                    if(!empty($_POST["registrar"])){
                        if(empty($_POST["servicio"]) OR empty($_POST["empresa"]) OR empty($_POST["detalle"]) 
                            OR empty($_POST["total"])){
        
                            $alert = '<p class="msg_error"> Falta llenar campos.</p>';
                        }else{
                            $servicio = $_POST["servicio"];
                            $empresa = $_POST["empresa"];
                            $detalle = $_POST["detalle"];
                            $total = $_POST["total"];
                            $sql = $conexion -> query("INSERT INTO egreso_fijo (fj_servicio,fj_empresa,fj_descripcion,fj_monto)
                                                        VALUES ('$servicio','$empresa','$detalle','$total')");

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
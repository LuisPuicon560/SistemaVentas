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
    <title>NUEVA EGRESO - PERSONAL | WEBSITE</title>
</head>
<body>
    <?php include "includes/header.php";?>
    <section id="container">
        <div class="title_page"><br><br>
            <h1><i class="fas fa-cube"></i> NUEVO EGRESO - PERSONAL</h1>
        </div>
        <br>
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4> Datos del Egreso Personal</h4>
            </div>
            <form method="post" class="datos" id="formulario" action="">
            
                <div class="wd301">
                    <label>Nombre del Personal:</label>
                    <input type="text" name="nom_personal" id="nom_personal" required><br>
                </div>
                <div class="wd30">
                    <label>Nº de Documento:</label>
                    <input type="text" name="num_documento" id="num_documento" required>
                </div>
                
                <div class="wd30">
                    <label>Cargo del Personal:</label>
                    <input type="text" name="cargo_personal" id="cargo_personal" required>
                </div>
                <div class="wd30">
                    <label>Total del Monto:</label>
                    <input type="text" name="total" id="total"required>
                </div><br>
                <div class="wd302">
                    <br>
                    <input type="submit" value="Enviar" name="registrar">
                </div>
                <?php

                    if(!empty($_POST["registrar"])){
                        if(empty($_POST["nom_personal"]) OR empty($_POST["num_documento"]) OR empty($_POST["cargo_personal"]) 
                            OR empty($_POST["total"])){
        
                            $alert = '<p class="msg_error"> Falta llenar campos.</p>';
                        }else{
                            $n_documento = $_POST["num_documento"];
                            $nombre = $_POST["nom_personal"];
                            $cargo = $_POST["cargo_personal"];
                            $total = $_POST["total"];
                            $sql = $conexion -> query("INSERT INTO egreso_personal (ep_nombre,ep_ndocumento,ep_cargo,ep_total)
                                                        VALUES ('$nombre','$n_documento','$cargo','$total')");

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
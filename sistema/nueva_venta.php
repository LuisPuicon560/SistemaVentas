<?php

    session_start();
    include "../conexion.php";
    //print md5($_SESSION['usuario_id']);
     date_default_timezone_set("America/Lima");

?>

<!DOCTYPE html>
<html lang="es" class=" ">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta charSet="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
    <?php include "includes/scripts.php";?>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <title>NUEVA VENTA | WEBSITE</title>

    <style>
    .hand {cursor: pointer;}

    </style>
</head>
<body>
    <?php include "includes/header.php";?>
    <section id="container">
        <div class="title_page">
            <h1><i class="fas fa-cube"></i> NUEVA VENTA</h1>
        </div>
        <br>
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4> Datos del Cliente</h4>
                <a href="#" class="btn_new btn_new_cliente"><i class="fas fa-plus"></i> Nuevo Cliente</a>
            </div>
            <form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
                <input  type="hidden" name="action" value="addCliente">
                <input type="hidden" id="idcliente" name="idcliente" value="" required>
                <div class="wd30">
                    <label>Ruc</label>
                    <input type="text" name="documento_cliente" id="documento_cliente">
                </div>
                <div class="wd30">
                    <label>Razón Social</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                </div>
                <div class="wd30">
                    <label>Teléfono</label>
                    <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
                </div>
                <div class="wd301">
                    <label>Dirección</label>
                    <input type="text" name="dire_cliente" id="dire_cliente" disabled required>
                </div>
                <div id="div_registro_cliente" class="wd100">
                    <button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar<button>
                </div>
            </form>
        </div>
        
        <!--*************************************************************************************************************-->
        
        <!--nuevo -->
        <table class="tbl_venta">
        <h4 class="buscarse">Buscar Servicios</h4>
            <input  type="search" name="txt_cod_producto" id="txt_cod_producto" placeholder="Ingrese el nombre del Servicio a buscar y luego presione Enter" class="tbl_venta">
            <br>
            <thead>
                <tr>
                    <th width="100px">Código</th>
                    <th>Nombre</th>
                    <th>Tiempo (Meses)</th>
                    <th class="textrigth">Precio Total</th>
                    <th> Accion</th>
                </tr>
                
          
            </thead><br>
            
            <h4 class="buscarse">Detalle Servicio</h4>
            <tbody id="htmlServicios">
                <td id="txt_descripcion" name="txt_descripcion"> - </td>
                <td id="txt_meses" name="txt_meses"> - </td>
                <td id="" name=""> - </td>
                <td id="txt_precio_total" name="txt_precio_total" class="textrigth">0.00</td>
                <td></td>
            </tbody>
           
        </table><br>
        <!--nuevo -->
        <br>

        <h4 class="buscarse">Datos de Venta</h4>
        <table class="tbl_venta">
            <thead>
                
                <tr>
                    <th>codigo</th>
                    <th colspan="2">Nombre</th>
                    <th colspan="3">Tiempo (Meses)</th>
                    <th class="textrigth">Precio Total</th>
                    <th> Accion</th>
                </tr>
            </thead>
            <tbody id="detalle_venta">
                <!--CONTENIDO AJAX-->
            </tbody>
            <tfoot id="detalle_totales">
                <!--CONTENIDO AJAX-->
            </tfoot>
        </table><br>
        <br>
        <!-- **************************** NOTAS ************************************************* 
        <div class="notas">
            <h4>AGREGAR UN COMENTARIO</h4>
            <div class="contenido">
                <textarea id="comentario" name="comentario" placeholder="Escriba una observacion"></textarea>
            </div>
        </div>-->

        <!--*************************************************************************************************************-->
        <div class="datos_venta">
            <h4>Datos del Vendedor</h4>
            <div class="datos">
                <div class="wd50">
                    <label> Vendedor</label>
                    <p><?php echo $_SESSION['nombre'];?></p>
                </div>
                <div class="wd50">
                <label for="tipo"> Tipo de Comprobante</label>
                <!--QUERY QUE NOS DEVUELVA TODO LOS ROLES-->
                <?php

                    $query_tc = mysqli_query($conexion,"SELECT * FROM ti_comprobante");
                    //
                    $result_tc  = mysqli_num_rows($query_tc);

                ?>
                
                <select name="tc" id="tc">
                    <?php
                        if($result_tc > 0){
                            
                            while ($tc =  mysqli_fetch_array($query_tc)){
                    ?>
                    <option value="<?php echo $tc['id_tc']?>"><?php echo $tc['nombre']?></option>
                    
                    <?php
                            }
                        }
                    ?>
                </select>
                </div>
                <div class="wd50">
                    <label> Acciones</label>
                    <div id="acciones_venta">
                        <a href="#" class="btn_ok textcenter" id="btn_anular_venta"><i class="fas fa-ban"></i> Anular</a>
                        <a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display:none"><i class="fas fa-edit"></i> Procesar</a>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
    <?php include "includes/footer.php";?>

    <!--********* PARA CUANDO NOS MOVAMOS A OTRA PAGINA Y TENGAMOS QUE BUSCAR OTRA COSA, NO SE BORREN LOS DATOS DE LA NUEVA VENTA-->
    <script type="text/javascript">

        $(document).ready(function(){
            var usuarioid = '<?php echo  $_SESSION['usuario_id'];?>';
            serchForDetalle(usuarioid);
        });

    </script>
</body>
</html>
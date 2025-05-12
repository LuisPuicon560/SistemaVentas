<?php
date_default_timezone_set('America/Lima');
$variable = date_default_timezone_get();
    include "../conexion.php";
    session_start();

    if(!empty($_POST)){

    //EXTRAER DATOS DEL SERVICIO
    //(NOTA PARA QUE NOSOTROS MOSTREMOS LO QUE ES EL NOMBRE DEL PRODUCTO DEBEMOS COMENTAR TODO EL QUERY Y PONER UN PRINT_R)
    if($_POST['action'] == 'infoProducto'){

    $servi_nombre = $_POST['producto'];
    $query = mysqli_query($conexion,"
    SELECT 
        cod_servicio, 
        servi_nombre, 
        servi_precio, 
        tiempo 
    FROM 
        servicio
    WHERE 
        servi_nombre like '%".$servi_nombre."%' AND servi_estado = 1");

    $result = mysqli_num_rows($query);
    $html ="";
    if($result > 0)
    {
        
        while ($fila = $query->fetch_assoc())
        {
            $cod_servicio   = $fila['cod_servicio'];
            $servi_nombre   = $fila['servi_nombre'];
            $tiempo         = $fila['tiempo'];
            $servi_precio   = $fila['servi_precio'];

            $html .= "<tr>
                <td>$cod_servicio</td>
                <td>$servi_nombre</td>
                <td>$tiempo</td>
                <td>$servi_precio</td>
                <td><i  class='fas fa-plus hand' style='color:green;' onclick='addProductoDetalle($cod_servicio);'></i></td>
            </tr>";
        }
        echo $html;
        exit;
    }
    elseif ($result == 0)
    {
        $html='<td id="txt_descripcion" name="txt_descripcion"> - </td>
        <td id="txt_meses" name="txt_meses"> - </td>
        <td id="" name=""> - </td>
        <td id="txt_precio_total" name="txt_precio_total" class="textrigth">0.00</td>
        <td></td>';
        echo $html;
        exit;
    }

    }

    //AGREGAR PRODUCTO AL DETALLE
    if($_POST['action'] == 'addProductoDetalle'){
        
    if(empty($_POST['producto'])){

            echo "error";
    }else{

        $codproducto = $_POST['producto'];
        $token = md5($_SESSION['usuario_id']);

        $query_igv = mysqli_query($conexion,"SELECT confi_igv FROM configuracion ");
        $result_igv = mysqli_num_rows($query_igv);

        $query_detalle_temp = mysqli_query($conexion, "CALL add_detalle_temp($codproducto,'$token')");
        $result = mysqli_num_rows($query_detalle_temp);

        $detalleTabla = '';
        $subtotal = 0;
        $igv = 0;
        $totalgeneral = 0;
        $arrayData = array();

        if ($result > 0){
            if($result_igv > 0){
                $info_igv = mysqli_fetch_assoc($query_igv);
                $igv = $info_igv['confi_igv'];
            }

            while ($data = mysqli_fetch_assoc($query_detalle_temp)){
                
                $precioTotal = round(($data['temp_preciototal'] * 2)- $data['temp_preciototal'],2) ;
                $subtotal = round($subtotal + $precioTotal,2);

                $detalleTabla .= '
                <tr>
                    <td>'.$data['cod_servicio'].'</td>
                        <td colspan="2">'.$data['servi_nombre'].'</td>
                        <td colspan="3">'.$data['tiempo'].'</td>
                        <td class="textright">'.$data['temp_preciototal'].'</td>
                        <td  class="">
                            <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['id_temp'].');"><i class="far fa-trash-alt"></i></a>
                        </td>
                </tr>';
            }
            $impuesto = round($subtotal * ($igv/100),2);
            $tl_snigv = round($subtotal - $impuesto,2);
            $totalgeneral = $tl_snigv + $impuesto;

            $detalleTotales = '
            <br>
                <tr>
                    <td colspan="6" class="textright"> SubTotal</td>
                    <td class="textright"> '.$tl_snigv.'</td>
                </tr>
                <tr>
                    <td colspan="6" class="textright"> IGV ('.$igv.'%)</td>
                    <td class="textright"> '.$impuesto.'</td>
                </tr>
                <tr>
                    <td colspan="6" class="textright"> TOTAL VENTA</td>
                    <td class="textright"> '.$totalgeneral.'</td>
                </tr>
            ';


            $arrayData['detalle'] = $detalleTabla;
            $arrayData['totales'] = $detalleTotales;

            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
        }else{
        echo "error";
        }
        mysqli_close($conexion);
    } 
    exit;
    } 

        //BUSCAR CLIENTE
        if($_POST['action'] == 'searchCliente'){
            
            if(!empty($_POST['cliente'])){
                
                $documento = $_POST['cliente'];

                $query = mysqli_query($conexion,"SELECT * FROM cliente WHERE cli_documento LIKE '$documento' AND cli_estado = 1");
                mysqli_close($conexion);
                $result = mysqli_num_rows($query);

                $data = '';
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                }else{
                    $data = 0;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            exit;
        }

        //REGISTRAR CLIENTE DESDE VENTA
        if($_POST['action'] == 'addCliente'){

            $documento = $_POST['documento_cliente'];
            $nombre = $_POST['nom_cliente'];
            $telefono = $_POST['tel_cliente'];
            $direccion = $_POST['dire_cliente'];
            $id_usuario = $_SESSION['usuario_id'];

            $query_insert = mysqli_query($conexion,"INSERT INTO cliente(cli_documento,cli_nombre,cli_telefono,cli_direccion,id_usuario) 
            VALUES('$documento','$nombre','$telefono','$direccion','$id_usuario') ");

            if($query_insert){

                $codCliente = mysqli_insert_id($conexion);
                $msg = $codCliente;
            }else{
                $msg = 'error';
            }
            mysqli_close($conexion);
            echo $msg;
            exit;   
        }  
        
        // Extraer detalles de la tabla detalle_temp

        if($_POST['action'] == 'serchForDetalle'){
        
            if(empty($_POST['user'])){
                    echo "error";
            }else{

                $token = md5($_SESSION['usuario_id']);

                $query = mysqli_query($conexion,"SELECT tmp.id_temp,tmp.token_user,s.tiempo,s.cod_servicio,tmp.temp_preciototal,s.servi_nombre
                                                FROM detalle_temp tmp INNER JOIN servicio s ON tmp.cod_servicio = s.cod_servicio
                                                WHERE token_user = '$token'");

                $result = mysqli_num_rows($query);

                $query_igv = mysqli_query($conexion,"SELECT confi_igv FROM configuracion ");
                $result_igv = mysqli_num_rows($query_igv);
                

                $detalleTabla = '';
                $subtotal = 0;
                $igv = 0;
                $totalgeneral = 0;
                $arrayData = array();

                if ($result > 0){
                    if($result_igv > 0){
                        $info_igv = mysqli_fetch_assoc($query_igv);
                        $igv = $info_igv['confi_igv'];
                    }

                    while ($data = mysqli_fetch_assoc($query)){
                        
                        $precioTotal = round(($data['temp_preciototal'] * 2)- $data['temp_preciototal'],2) ;
                        $subtotal = round($subtotal + $precioTotal,2);

                        $detalleTabla .= '
                        <tr>
                            <td>'.$data['cod_servicio'].'</td>
                                <td colspan="2">'.$data['servi_nombre'].'</td>
                                <td colspan="3">'.$data['tiempo'].'</td>
                                <td class="textright">'.$data['temp_preciototal'].'</td>
                                <td  class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['id_temp'].');"><i class="far fa-trash-alt"></i></a>
                                </td>
                        </tr>';
                    }
                    $impuesto = round($subtotal * ($igv/100),2);
                    $tl_snigv = round($subtotal - $impuesto,2);
                    $totalgeneral = $tl_snigv + $impuesto;

                    $detalleTotales = '
                        <tr>
                            <td colspan="6" class="textright"> SubTotal</td>
                            <td class="textright"> '.$tl_snigv.'</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="textright"> IGV ('.$igv.'%)</td>
                            <td class="textright"> '.$impuesto.'</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="textright"> TOTAL VENTA</td>
                            <td class="textright"> '.$totalgeneral.'</td>
                        </tr>
                    ';


                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
                }else{
                echo "error";
                }
                mysqli_close($conexion);
            } 
            exit;
        } 

        //DELETE DEL DETALLE_TEMP
        if($_POST['action'] == 'delProductoDetalle'){

           if(empty($_POST['id_detalle'])){

                echo "error";
            }else{

                $id_detalle = $_POST['id_detalle'];
                $token = md5($_SESSION['usuario_id']);

                $query_igv = mysqli_query($conexion,"SELECT confi_igv FROM configuracion ");
                $result_igv = mysqli_num_rows($query_igv);

                $query_detalle_temp = mysqli_query($conexion,"CALL del_detalle_temp($id_detalle,'$token')");
                $result = mysqli_num_rows($query_detalle_temp);

                $detalleTabla = '';
                $subtotal = 0;
                $igv = 0;
                $totalgeneral = 0;
                $arrayData = array();

                if ($result > 0){
                    if($result_igv > 0){
                        $info_igv = mysqli_fetch_assoc($query_igv);
                        $igv = $info_igv['confi_igv'];
                    }

                    while ($data = mysqli_fetch_assoc($query_detalle_temp)){
                    
                        $precioTotal = round(($data['temp_preciototal'] * 2)- $data['temp_preciototal'],2) ;
                        $subtotal = round($subtotal + $precioTotal,2);

                        $detalleTabla .= '
                        <tr>
                            <td>'.$data['cod_servicio'].'</td>
                                <td colspan="2">'.$data['servi_nombre'].'</td>
                                <td colspan="3">'.$data['tiempo'].'</td>
                                <td class="textright">'.$data['temp_preciototal'].'</td>
                                <td  class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['id_temp'].');"><i class="far fa-trash-alt"></i></a>
                                </td>
                        </tr>';
                    }
                    $impuesto = round($subtotal * ($igv/100),2);
                    $tl_snigv = round($subtotal - $impuesto,2);
                    $totalgeneral = $tl_snigv + $impuesto;

                    $detalleTotales = '
                        <tr>
                            <td colspan="6" class="textright"> SubTotal</td>
                            <td class="textright"> '.$tl_snigv.'</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="textright"> IGV ('.$igv.'%)</td>
                            <td class="textright"> '.$impuesto.'</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="textright"> TOTAL VENTA</td>
                            <td class="textright"> '.$totalgeneral.'</td>
                        </tr>
                    ';


                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
                }else{
                    echo "error";
                }
                mysqli_close($conexion);
            } 
            exit; 
        }
        
        //ANULAR VENTA 
        if($_POST['action'] == 'anularVenta'){

            $token = md5($_SESSION['usuario_id']);
            $query_del = mysqli_query($conexion,"DELETE FROM detalle_temp WHERE token_user = '$token'");
            //mysqli_close($conexion);
            if ($query_del){
                echo 'ok';
            }else{
                echo 'error';
            }
            exit;
        }

        //INFO DEL LA DIRECCION DE LOS CLIENTES
        if($_POST['action'] == 'infoDireccionCliente'){
            if(!empty($_POST['idcliente'])){

                $idcliente = $_POST['idcliente'];
                $query = mysqli_query($conexion,"SELECT cli_direccion FROM cliente WHERE id_cliente = '$idcliente' AND cli_estado = 1");

                mysqli_close($conexion);

                $result = mysqli_num_rows($query);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

        //PROCESAR VENTA
        if($_POST['action'] == 'procesarVenta'){
            
            //POR SI EL CODIGO DEL CLIENTE VA CON CF
            if(empty($_POST['codcliente']) || empty($_POST['codComprobante']) ){
                $codcliente = 1;
            }else{
                $codcliente = $_POST['codcliente'];
                $codComprobante = $_POST['codComprobante'];
            }
            $token = md5($_SESSION['usuario_id']);
            $usuario = $_SESSION['usuario_id'];
            
            $query = mysqli_query($conexion,"SELECT * FROM detalle_temp WHERE token_user = '$token'");
            $result = mysqli_num_rows($query);

            if($result > 0){
                $query_procesar = mysqli_query($conexion,"CALL procesar_venta($usuario,$codcliente,'$token',$codComprobante)");
                $result_detalle = mysqli_num_rows($query_procesar);

                if($result_detalle > 0){
                    $data = mysqli_fetch_assoc($query_procesar);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                }else{
                    echo "error";
                }
            }else{
                echo "error";
            }
            mysqli_close($conexion);
            exit;
        }
        //CAPTURAR CONTRASEÑA
        if($_POST['action'] == 'changePassword'){

            if(!empty($_POST['passActual']) && !empty($_POST['passNuevo'])){
                $password = md5($_POST['passActual']);
                $newPass = md5($_POST['passNuevo']);
                $idUser = $_SESSION['usuario_id'];

                $code = '';
                $msg = '';
                $arrData = array();

                $query_user = mysqli_query($conexion, "SELECT * FROM usuario WHERE usu_clave ='$password' AND id_usuario = $idUser");
                $result = mysqli_num_rows($query_user);
                if ($result > 0){
                    $query_update = mysqli_query($conexion,"UPDATE usuario SET usu_clave = '$newPass' WHERE id_usuario = $idUser");
                    mysqli_close($conexion);

                    if($query_update){
                        $code = '00';
                        $msg = "Su contraseña se actualizado con exito.";
                    }else{
                        $code = '2';
                        $msg="No es posible cambiar su contraseña.";
                    }
                }else{
                    $code = '1';
                    $msg = "La contraseña actual es incorrecta.";
                }
                $arrData = array('cod'=>$code,'msg'=>$msg);
                echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
            }else{
                echo "error";
            }
            exit;
        }

        //ACTUALIZAR DATOS DE LA EMPRESA
        if($_POST['action'] == 'updateDataEmpresa'){

            if(empty($_POST['txtRuc']) || empty($_POST['txtNombreLegal']) || empty($_POST['txtNombreComercial']) || empty($_POST['txtTelefono']) || empty($_POST['txtCorreo']) || empty($_POST['txtDireccion']) || empty($_POST['txtIGV'])){
                $code = '1';
                $msg = "Todos los campos son obligatorios.";
            }else{
                $ruc = intval($_POST['txtRuc']);
                $nombrelegal = $_POST['txtNombreLegal'];
                $nombrecomercial = $_POST['txtNombreComercial'];
                $telefono  = intval($_POST['txtTelefono']);
                $email = $_POST['txtCorreo'];
                $direccion = $_POST['txtDireccion'];
                $igv = $_POST['txtIGV'];

                $queryUpd = mysqli_query($conexion,"UPDATE configuracion SET confi_ndocumento = $ruc, confi_nombrelegal = '$nombrelegal',
                                                    confi_nombrecomer = '$nombrecomercial', confi_telefono = $telefono, confi_correo = '$email',
                                                    confi_direccion = '$direccion', confi_igv = $igv WHERE id_configuracion = 1");
                mysqli_close($conexion);
                if($queryUpd){
                    $code = '00';
                    $msg = "Datos actualizados correctamente.";
                }else{
                    $code = '2';
                    $msg ="Error al actualizar los datos.";
                }
            }
            $arrData = array('cod' => $code, 'msg' => $msg);
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
            exit;
        }

        //INFO DE LA  FACTURA
        if($_POST['action'] == 'infoFactura'){
            if(!empty($_POST['nofactura'])){

                $nofactura = $_POST['nofactura'];
                $query = mysqli_query($conexion,"SELECT * FROM comprobante WHERE id_comprobante = '$nofactura' AND com_estado = 1");

                mysqli_close($conexion);

                $result = mysqli_num_rows($query);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

        //INFO DEL DETALLE DE LOS EGRESOS
        if($_POST['action'] == 'infoDetalleEgreso'){
            if(!empty($_POST['idegreso'])){

                $idegreso = $_POST['idegreso'];
                $query = mysqli_query($conexion,"SELECT detalle FROM ext WHERE id_externo = '$idegreso' AND estado = 1");

                mysqli_close($conexion);

                $result = mysqli_num_rows($query);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

        //INFO DEL LA DIRECCION DE LOS EGRESOS
        if($_POST['action'] == 'infoDireccionEgreso'){
            if(!empty($_POST['idegreso'])){

                $idegreso = $_POST['idegreso'];
                $query = mysqli_query($conexion,"SELECT direccion FROM ext WHERE id_externo = '$idegreso' AND estado = 1");

                mysqli_close($conexion);

                $result = mysqli_num_rows($query);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

        //INFO DEL LA DETALLE DE LOS EGRESOS FIJOS
        if($_POST['action'] == 'infoDetalleEgreso1'){
            if(!empty($_POST['fijo'])){

                $fijo = $_POST['fijo'];
                $query = mysqli_query($conexion,"SELECT fj_descripcion FROM egreso_fijo WHERE id_fijo = '$fijo' AND fj_estado = 1");

                mysqli_close($conexion);

                $result = mysqli_num_rows($query);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

         //INFO DEL LA DETALLE DE LOS EGRESOS VARIABLE
         if($_POST['action'] == 'infoDetalleEgresoV'){
            if(!empty($_POST['variable'])){

                $variable = $_POST['variable'];
                $query = mysqli_query($conexion,"SELECT descripcion FROM egreso_variable WHERE id_variable = '$variable' AND estado = 1");

                mysqli_close($conexion);

                $result = mysqli_num_rows($query);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }
        //INFO DE LA  FACTURA DE EGRESOS
        if($_POST['action'] == 'infoFacturaEgreso'){
            if(!empty($_POST['noegreso'])){

                $noegreso = $_POST['noegreso'];
                $query = mysqli_query($conexion,"SELECT * FROM externo WHERE id_externo = '$noegreso' AND ext_estado = 1");

                mysqli_close($conexion);

                $result = mysqli_num_rows($query);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

        //ANULAR FACTURA
        if($_POST['action'] == 'anularFactura'){

            if(!empty($_POST['noFactura'])){

                $noFactura = $_POST['noFactura'];

                $query_anular = mysqli_query($conexion,"CALL anular_factura($noFactura)");
                mysqli_close($conexion);
                $result = mysqli_num_rows($query_anular);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query_anular);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

        //ANULAR FACTURA DE EGRESO
        if($_POST['action'] == 'anularFacturaEgreso'){

            if(!empty($_POST['noEgreso'])){

                $noEgreso = $_POST['noEgreso'];

                $query_anular = mysqli_query($conexion,"CALL anular_factura_egre($noEgreso)");
                mysqli_close($conexion);
                $result = mysqli_num_rows($query_anular);
                if($result > 0){
                    $data = mysqli_fetch_assoc($query_anular);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            echo "error";
            exit;
        }

        //BUSCAR USUARIO DESDE NUEVO EGRESO
        if($_POST['action'] == 'searchUsuario'){
            
            if(!empty($_POST['usuario'])){
                
                $documento = $_POST['usuario'];
                $query = mysqli_query($conexion,"SELECT * FROM usu_egreso WHERE u_documento LIKE '$documento' AND u_estado = 1");

                mysqli_close($conexion);
                $result = mysqli_num_rows($query);
                
                $data ='';
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                }else{
                    $data = 0;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            exit;
        } 

        //REGISTRAR USUARIO DESDE NUEVO EGRESO
        if($_POST['action'] == 'addUsuario'){

            $documento = $_POST['documento_usuario'];
            $nombre = $_POST['nom_usuario'];
            $telefono = $_POST['tel_usuario'];
            $direccion = $_POST['dire_usuario'];
            $id_usuario = $_SESSION['usuario_id'];

            $query_insert = mysqli_query($conexion,"INSERT INTO usu_egreso(u_documento,u_nombre,u_telefono,u_direccion,id_usuario) 
            VALUES('$documento','$nombre','$telefono','$direccion','$id_usuario') ");

            if($query_insert){

                $codUsuario = mysqli_insert_id($conexion);
                $msg = $codUsuario;
            }else{
                $msg = 'error';
            }
            mysqli_close($conexion);
            echo $msg;
            exit;   
        } 

        //AGREGAR PRODUCTOS A LA TABLA DETALLE DE NUEVO EGRESO
        if($_POST['action'] == 'addProducto'){
            
            if(empty($_POST['nombre']) || empty($_POST['cantidad'])){
                echo "error";
            }else{
                $nombre = $_POST['nombre'];
                $empresa = $_POST['empresa'];
                $cantidad = $_POST['cantidad'];
                $preciouni = $_POST['preciouni'];
                $token_user = md5($_SESSION['usuario_id']);

                $queryy_igv = mysqli_query($conexion,"SELECT confi_igv FROM configuracion");
                $resultt_igv = mysqli_num_rows($queryy_igv);

                $query_detalle_tempp = mysqli_query($conexion,"CALL add_detalle_tempp('$nombre','$empresa',$cantidad,$preciouni,'$token_user')");
                $resultt = mysqli_num_rows($query_detalle_tempp);

                $detalletabla = '';
                $sub_totall = 0;
                $igvv = 0;
                $totall = 0;
                $arrayDataa = array();

                if($resultt > 0){
                    if($resultt_igv > 0){
                        $infoo_igv = mysqli_fetch_assoc($queryy_igv);
                        $igvv = $infoo_igv['confi_igv'];
                    }
                    while ($data = mysqli_fetch_assoc($query_detalle_tempp)){

                        $preciototal = round($data['cantidad'] * $data['precio_uni'],2);
                        $sub_totall = round($sub_totall  + $preciototal,2);
                        $totall = round($totall  + $preciototal,2);

                        $detalletabla .= '
                            <tr>
                                <td>'.$data['empresa'].'</td>
                                <td colspan="2">'.$data['nombre_pro'].'</td>
                                <td class="textcenter">'.$data['cantidad'].'</td>
                                <td class="textright">'.$data['precio_uni'].'</td>
                                <td class="textright">'.$preciototal.'</td>
                                <td  class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalles('.$data['id_tempp'].');"><i class="far fa-trash-alt"></i></a>
                                </td>
                            </tr>';
                    }
                    $impuestoo = round($sub_totall * ($igvv / 100),2);
                    $tl_snigvv = round($sub_totall - $impuestoo,2);
                    $totall = round($tl_snigvv + $impuestoo,2);

                    $detalletotales = '
                        <tr>
                            <td colspan="5" class="textright"> SubTotal</td>
                            <td class="textright"> '.$tl_snigvv.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright"> IGV ('.$igvv.'%)</td>
                            <td class="textright"> '.$impuestoo.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright"> TOTAL VENTA</td>
                            <td class="textright"> '.$totall.'</td>
                        </tr>';

                    $arrayDataa['detalle'] = $detalletabla;
                    $arrayDataa['totales'] = $detalletotales;

                    echo json_encode($arrayDataa,JSON_UNESCAPED_UNICODE);
                }else{
                    echo'error';
                }
                mysqli_close($conexion);
            }
            exit;
        }

        // EXTRAER DETALLES DE NUEVO EGRESO

        if($_POST['action'] == 'serchForDetalles'){
        
            if(empty($_POST['user'])){

                    echo "error";
            }else{

                $tokenn = md5($_SESSION['usuario_id']);

                $queryy = mysqli_query($conexion,"SELECT tmp.id_tempp,tmp.token_usuario,tmp.nombre_pro, tmp.empresa,tmp.cantidad,tmp.precio_uni
                                                FROM detalle_tempp tmp WHERE token_usuario = '$tokenn'");

                $resultt = mysqli_num_rows($queryy);

                $query_igvv = mysqli_query($conexion,"SELECT confi_igv FROM configuracion ");
                $result_igvv = mysqli_num_rows($query_igvv);
                

                $detalletabla = '';
                $subtotall = 0;
                $igvv = 0;
                $totalgenerall = 0;
                $arraydata = array();

                if ($resultt > 0){
                    if($result_igvv > 0){
                        $info_igvv = mysqli_fetch_assoc($query_igvv);
                        $igvv = $info_igvv['confi_igv'];
                    }

                    while ($data = mysqli_fetch_assoc($queryy)){
                        
                        $preciototal = round($data['cantidad'] * $data['precio_uni'],2);
                        $subtotall = round($subtotall + $preciototal,2);
                        $totalgenerall = round($totalgenerall + $preciototal,2);

                        $detalletabla .= '
                        <tr>
                                <td>'.$data['empresa'].'</td>
                                <td colspan="2">'.$data['nombre_pro'].'</td>
                                <td class="textcenter">'.$data['cantidad'].'</td>
                                <td class="textright">'.$data['precio_uni'].'</td>
                                <td class="textright">'.$preciototal.'</td>
                                <td  class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalles('.$data['id_tempp'].');"><i class="far fa-trash-alt"></i></a>
                                </td>
                        </tr>';
                    }
                    $impuesto = round($subtotall * ($igvv/100),2);
                    $tl_snigv = round($subtotall - $impuesto,2);
                    $totalgenerall = round($tl_snigv + $impuesto,2);

                    $detalletotales = '
                        <tr>
                            <td colspan="5" class="textright"> SubTotal</td>
                            <td class="textright"> '.$tl_snigv.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright"> IGV ('.$igvv.'%)</td>
                            <td class="textright"> '.$impuesto.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright"> TOTAL VENTA</td>
                            <td class="textright"> '.$totalgenerall.'</td>
                        </tr>
                    ';


                    $arraydata['detalle'] = $detalletabla;
                    $arraydata['totales'] = $detalletotales;

                    echo json_encode($arraydata, JSON_UNESCAPED_UNICODE);
                }else{
                echo "error";
                }
                mysqli_close($conexion);
            } 
            exit;
        } 

        //DELETE DEL DETALLE_TEMPP
        if($_POST['action'] == 'delProductoDetalles'){

            if(empty($_POST['id_detalles'])){

                echo "error";
            }else{

                $id_detalles = $_POST['id_detalles'];
                $token = md5($_SESSION['usuario_id']);

                $query_igvv = mysqli_query($conexion,"SELECT confi_igv FROM configuracion ");
                $result_igvv = mysqli_num_rows($query_igvv);

                $query_detalle_tempp = mysqli_query($conexion,"CALL del_detalle_tempp($id_detalles,'$token')");
                $resultt = mysqli_num_rows($query_detalle_tempp);

                $detalletabla = '';
                $subtotall = 0;
                $igvv = 0;
                $totalgenerall = 0;
                $arraydata = array();

                if ($resultt > 0){
                    if($result_igvv > 0){
                        $info_igvv = mysqli_fetch_assoc($query_igvv);
                        $igvv = $info_igvv['confi_igv'];
                    }

                    while ($data = mysqli_fetch_assoc($query_detalle_tempp)){
                    
                        $preciototal = round($data['cantidad'] * $data['precio_uni'],2);
                        $subtotall = round($subtotall + $preciototal,2);
                        $totalgenerall = round($totalgenerall + $preciototal,2);

                        $detalletabla .= '
                        <tr>
                            <td>'.$data['empresa'].'</td>
                                <td colspan="2">'.$data['nombre_pro'].'</td>
                                <td class="textcenter">'.$data['cantidad'].'</td>
                                <td class="textright">'.$data['precio_uni'].'</td>
                                <td class="textright">'.$preciototal.'</td>
                                <td  class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalles('.$data['id_tempp'].');"><i class="far fa-trash-alt"></i></a>
                                </td>
                        </tr>';
                    }
                    $impuesto = round($subtotall * ($igvv/100),2);
                    $tl_snigv = round($subtotall - $impuesto,2);
                    $totalgenerall = round($tl_snigv + $impuesto,2);

                    $detalletotales = '
                        <tr>
                            <td colspan="5" class="textright"> SubTotal</td>
                            <td class="textright"> '.$tl_snigv.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright"> IGV ('.$igvv.'%)</td>
                            <td class="textright"> '.$impuesto.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright"> TOTAL VENTA</td>
                            <td class="textright"> '.$totalgenerall.'</td>
                        </tr>
                        ';


                    $arraydata['detalle'] = $detalletabla;
                    $arraydata['totales'] = $detalletotales;

                    echo json_encode($arraydata, JSON_UNESCAPED_UNICODE);
                }else{
                    echo "error";
                }
                mysqli_close($conexion);
            } 
            exit; 
        }

        //ANULAR VENTA DE EGRESO        
        if($_POST['action'] == 'anularVentas'){

            $token = md5($_SESSION['usuario_id']);
            $query_del = mysqli_query($conexion,"DELETE FROM detalle_tempp WHERE token_usuario = '$token'");
            //mysqli_close($conexion);
            if ($query_del){
                echo 'ok';
            }else{
                echo 'error';
            }
            exit;
        }

        //PROCESAR VENTA EGRESO
        if($_POST['action'] == 'procesarVentaEgreso'){
            
            $tokenn = md5($_SESSION['usuario_id']);
            $usuarios = $_SESSION['usuario_id'];
            $codusuario = $_POST['codusuario'];
            
            $qquery = mysqli_query($conexion,"SELECT * FROM detalle_tempp WHERE token_usuario = '$tokenn'");
            $rresult = mysqli_num_rows($qquery);

            if($rresult > 0){
                $qquery_procesar = mysqli_query($conexion,"CALL procesar_venta_egreso($usuarios,$codusuario,'$tokenn')");
                $result_detalles = mysqli_num_rows($qquery_procesar);

                if($result_detalles > 0){
                    $data = mysqli_fetch_assoc($qquery_procesar);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                }else{
                    echo "error";
                }
            }else{
                echo "error";
            }
            mysqli_close($conexion);
            exit;
        }
    }
    if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
    
        // Consulta SQL con los límites de fecha
        $sql = mysqli_query($conexion, "SELECT DATE(fechaemi) AS fecha, SUM(total) AS suma_total
                                        FROM ext
                                        WHERE fechaemi BETWEEN '$startDate' AND '$endDate'
                                        GROUP BY fecha");
    
        $result = mysqli_num_rows($sql);
    
        // Array para almacenar las etiquetas de fecha y las sumas totales
        $labels = [];
        $totals = [];
    
        if ($result > 0) {
            while ($data = mysqli_fetch_array($sql)) {
                $labels[] = $data['fecha'];
                $totals[] = $data['suma_total'];
            }
        }
    
        // Enviar los datos de respuesta en formato JSON
        echo json_encode(array('labels' => $labels, 'totals' => $totals));
        exit(); // Terminar la ejecución del script después de enviar la respuesta JSON
    }

    //*formulario1
    if (isset($_POST['start_date1']) && isset($_POST['end_date1'])) {
        $startDate = $_POST['start_date1'];
        $endDate = $_POST['end_date1'];
    
        // Consulta SQL con los límites de fecha
        $sql = mysqli_query($conexion, "SELECT DATE(com_fechaemi) AS fecha, SUM(com_totalfactura) AS suma_total
                                        FROM comprobante
                                        WHERE com_fechaemi BETWEEN '$startDate' AND '$endDate'
                                        GROUP BY fecha");
    
        $result = mysqli_num_rows($sql);
    
        // Array para almacenar las etiquetas de fecha y las sumas totales
        $labels = [];
        $totals = [];
    
        if ($result > 0) {
            while ($data = mysqli_fetch_array($sql)) {
                $labels[] = $data['fecha'];
                $totals[] = $data['suma_total'];
            }
        }
    
        // Enviar los datos de respuesta en formato JSON
        echo json_encode(array('labels' => $labels, 'totals' => $totals));
             // Terminar la ejecución del script después de enviar la respuesta JSON
    }
    exit;
?>
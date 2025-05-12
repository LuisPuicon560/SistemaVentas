<?php

session_start();

include "../../conexion.php";

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
    <meta charset="UTF-8">
</head>

<body>
        <table class="table table-light">
            <tbody>
                <tr>
                    <th>Nº</th>
                    <th>Fecha / Hora</th>
                    <th>Gastos Operativos</th>
                    <th>Detalle</th>
                    <th class="textright">Total De Egreso</th>
                </tr>
                <?php
                //LISTAR REGISTROS

                $query = mysqli_query($conexion, "SELECT id_variable, gastos, descripcion, total, fecha FROM egreso_variable
                                                    WHERE estado != 10 
                                                    AND 
     fecha >='" . $fechaIni . " 00:00:00'
     AND 
     fecha <='". $fechaFin . " 23:59:59'
                                                    ORDER BY fecha DESC");
                //mysqli_close($conexion);
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_array($query)) {
                ?>
                        <tr id="row_<?php echo $data["id_variable"]; ?>">
                            <td><?php echo $data["id_variable"]; ?></td>
                            <td><?php echo $data["fecha"]; ?></td>
                            <td><?php echo $data["gastos"]; ?></td>
                            <td><?php echo $data["descripcion"]; ?></td>                               
                            <td class="textright totalfactura"><span>S/.</span><?php echo $data["total"]; ?></td>
                        </tr>
                <?php
                    }
                }

                ?>
            </tbody>
        </table>
    </section>
</body>

</html>

<style>
	@import url(../fonts/GothamBook.css);
@import url(../fonts/GothamBold.css);
*{
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}
body{
	background: #ededed;
	
}
h1, h2, h3, h4, h5, h6 {
    font-family: 'arial';
    font-weight: bold;
    letter-spacing: 1px;
}
h1{
	font-size: 26px;
}
h2{
	font-size: 20px;
}
h3{
	font-size: 18px;
}
h4{
	font-size: 16px;
}
h5{
	font-size: 14px;
}
h6{
	font-size: 12px;
}
p{
	font-family: 'arial';
	letter-spacing: 2px;
	font-size: 14px;
	line-height: 20px;
}
a{
	text-decoration: none;
	font-family: arial;
	letter-spacing: 1px;
}
span {
    font-family: 'GothamBook';
    letter-spacing: 1px;
    font-size: 14px;
    line-height: 20px;
}
header{
	position: fixed;
	width: 100%;
}
.header{
	color: #FFF;
	background: #161616;
	height: 35px;
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 10px;
}
.optionsBar{
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: -o-flex;
    display: flex;
    justify-content: center;
    align-items: center;
}
.optionsBar span {
    color: #FFF;
    font-size: 11pt;
    font-family: 'GothamBook';
    text-transform: uppercase;
    margin-left: 30px;
}
.icono{
	position:absolute;
	right: 84%;
	padding-top: 10px;
}
.photouser {
    margin-left: 30px;
    width: 25px;
    height: 25px;
}
.hola{
	background-image: url(https://ws.net.pe/sistema/factura/img/LOGOWB.png);
}
.close{
	width: 25px;
    height: 25px;
}
.optionsBar a {
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: -o-flex;
    display: flex;
    margin-left: 30px;
}
nav ul{
	background: #11B90A;
	/*background: #05817d;*/
	list-style: none;
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	display: flex;
	justify-content: left;
	align-items: center;
}
nav ul > li a{
	position: relative;
}
.estilo{
	color: #FFF;
	display: block;
	font-size: 10pt;
	font-family: 'GothamBook';
	padding: 15px 25px;
	text-transform: uppercase;
	letter-spacing: 2px;
	transition: background .5s;
	border-right: 2px solid #fff;
}
.estilo2{
	color: #FFF;
	display: block;
	font-size: 10pt;
	padding: 15px 30px;
	text-transform: uppercase;
	letter-spacing: 2px;
}
nav .principal > a{
    background: url(../images/arrow_bottom.png) no-repeat;
    background-position-x: 0%;
    background-position-y: 0%;
    background-size: auto auto;
    background-position: 94% center;
    background-size: 10px;
}
nav ul li:hover ul{
	display: block;
}
nav li ul{
	/*background: #177470;
	*/background: #11B90A;
	display: none;
	flex-direction: column;
	position: absolute;
	align-items: flex-start;
	border-radius: 0 0 10px 10px;
	-webkit-border-radius: 0 0 10px 10px;
	-moz-border-radius: 0 0 10px 10px;
	-ms-border-radius: 0 0 10px 10px;
	-o-border-radius: 0 0 10px 10px;
}
nav li ul a{
	position: relative;
	padding: 10px 30px;
	border-right: initial;
}
nav li ul a:hover{
	/*background: #2c9595;*/
	background: #176315;
}
nav li ul li:last-child{
	border-radius: 0 0 10px 10px;
	overflow: hidden;
}
#container{
	padding: 90px 15px 15px;
}
/*****************************************************************/
.form_register{
	width: 450px;
	margin: auto;
	margin-top: 20px;
}
.form_register h1{
	color: #135b11;
}
hr{
	border: 0;
	background: #ccc;
	height: 1px;
	margin: 10px 0;
	display: block;
}
form{
	background: #fff;
	margin: auto;
	padding: 20px 50px;
	border: 1px solid #d1d1d1;
}
label{
	display: block;
	font-size: 20px;
	font-family: 'GothamBook';
	margin: 15px auto 5px auto;
}
input,select{
	display: block;
	width: 100%;
	font-size: 11pt;
	padding: 5px;
	border: 2px solid #85929e;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	-ms-border-radius: 5px;
	-o-border-radius: 5px;
}
/*NO MUESTRE EL PRIMER ELEMENTO DEL ROL*/
.notItemOne option.first-child{
	display: none;
}
.buscarse{
	padding-left: 16%;
	
}
.btn_save{
	font-size: 12pt;
	background: #45bb41;
	padding: 10px;
	color: #fff;
	letter-spacing: 1px;
	border: 0;
	cursor: pointer;
	margin: 15px auto;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	-ms-border-radius: 5px;
	-o-border-radius: 5px;
	margin-top: 30px;
}
.btn_save:hover{
	background: #85929e;
}
.btn_atras{
	font-size: 12pt;
	background: #e65656;
	padding: 10px;
	color: #fff;
	letter-spacing: 1px;
	border: 0;
	cursor: pointer;
	margin: 15px auto;
}
.btn_atras:hover{
	background: #85929e;
}

.alert{
	width: 100%;
	background: #65e07d66;
	border-radius: 6px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	-ms-border-radius: 6px;
	-o-border-radius: 6px;
	margin: 20px auto;
}
.msg_error{
	color: #e65656;
}
.msg_save{
	color: #126e00;
}
.alert p{
	padding: 10px;
}

/*---------------LISTAR_USUARIO------------*/
#container h1{
	font-size: 50px;
	display: inline-block;
}
.btn_new{
	display: inline-block;
	background: #38a235;
	color: #fff;
	padding: 5px 25px;
	border-radius: 4px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	-ms-border-radius: 4px;
	-o-border-radius: 4px;
	margin: 20px;
}
table{
	border-collapse: collapse;
	font-family: 'arial';
	font-size: 12pt;
	width: 100%;
}
table th{
	text-align: left;
	padding: 10px;
	background: #047001;
	color: #fff;
}
/*CELDAS DE COLORES*/
table tr:nth-child(odd){
	background: #fff;
}
table td{
	padding: 10px;
}
.link_edit{
	color: #0ca4ce;
}
.link_eliminar{
	color: #f26b6b;
}

.link_add{
	color: #64b13c;
}
/*------- eliminar  ---------- */

.data_delete{
	text-align: center;
	margin-top: 80px;

}
.data_delete h2{
    font-size: 12pt;
}
.data_delete span{
    font-weight: bold;
    color: #4f72d4;
    font-size: 12pt;
}
.btn_cancelar,.btn_ok,.btn_cancel{
    width: 124px;
    background: #478ba2;
    color: #fff;
    display: inline-block;
    padding: 5px;
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    -ms-border-radius: 5px;
    -o-border-radius: 5px;
    cursor: pointer;
    margin: 5px;
}
.btn_ok{
	background: #e65656 ;
}
.btn_ok:hover{
	background: #6a2929 ;
}
.btn_cancelar,.btn_cancel{
	background: #42b343;
}
.btn_cancelar:hover{
	background: #1e561f;
}
.data_delete form{
	background: initial;
	margin: auto;
	padding: 20px 50px;
	border: 0;
}
/* -------- PAGINADOR ---------*/
.paginador ul{
	padding: 15px;
	list-style: none;
	background: #fff;
	margin-top: 15px;
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	display: flex;
	justify-content: flex-end;
}
.paginador a, .pageselect{
	color: #428bca;
	border: 1px solid #ddd;
	padding: 5px;
	display: inline-block;
	font-size: 14px;
	text-align: center;
	width: 35px;
}
.paginador a:hover{
	background: #ddd;

}
.pageselect{
	color: #fff;
	background: #428bca;
	border: 1px solid #428bca;
}

/*--------- BUSQUEDAD ---------*/

.form_search{
	display: flex;
	float: right;
	background: initial;
	padding: 10px;
	border-radius: 10px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	-ms-border-radius: 10px;
	-o-border-radius: 10px;
	width: 400px;
}
.form_search .btn_search{
	background: #1faac8;
	color: #fff;
	padding: 0 20px;
	border: 5px;
	cursor: pointer;
	margin-left: 10px;
	width: 100px;
}
.form_search .btn_cl{
	background: #e65656;
	color: #fff;
	padding: 10px;
	border: 0;
	cursor: pointer;
	margin-left: 10px;
	width: 70px;
	text-align: center;
	
}
.btn_search:hover{
	background: #156274;
}
.form_search input {
	width: 100%;
}

/*============ Ventas ============*/
.datos_cliente, .datos_venta, .title_page, .contenedor{
	width: 100%;
	max-width: 900px;
	margin: auto;
	margin-bottom: 20px;
}
#detalle_venta tr{
	background-color: #FFF !important;
}
#detalle_venta td{
	border-bottom: 1px solid #CCC;
}
.boton{
	width: calc(100% - 20px);
	padding: 9px;
	margin: auto;
	margin-top: 12px;
	font-size: 16px;
}

.datos{
	background-color: #e3ecef;
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	display: flex;
	display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    border: 2px solid #78909C;
    padding: 10px;
    border-radius: 10px;
    margin-top: 10px;
}
.buscar{
	background-color: #e3ecef;
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	display: flex;
	display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    border: 2px solid #78909C;
    padding: 10px;
    border-radius: 10px;
    margin-top: 10px;
}

.action_cliente{
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	display: flex;
	align-items: center;
}

.datos label{
	margin: 5px auto;
}
.wd20{
	width: 20%;
}
.wd25{
	width: 25%;
}
.wd30{
	width: 30%;
}
#txt_descuento{
	width: 30%;
}
.wd301{
	width: 100%;
}
#tipodoc{
	width: 50%;
}
.wd40{
	width: 40%;
}
.wd60{
	width: 60%;
}
.wd100{
	width: 100%;
}
#div_registro_cliente, #add_product_venta, #div_registro_usuario{
	display: none;
}
.displayN{
	display: none;
}
.tbl_venta{
	max-width: 900px;
	margin: auto;
}
.tbl_venta tfoot td{
	font-weight: bold;
}
.textright{
	text-align: right;
}
.textcenter{
	text-align: center;
}
.textleft{
	text-align: left;
}
.btn_anular{
	background-color: #f36a6a;
	border: 0;
	border-radius: 5px;
	cursor: pointer;
	padding: 10px;
	margin: 0 3px;
	color: #FFF;
}

/******* ESTILOS DEL PANEL DE CONTROL **********/

.tittlePanelControl{ 
	width: 100%;
	background: #fff;
	padding: 5px 15px;
	margin-bottom: 10px;
	font-size: 18pt !important;
	color: #0A4661;
}

.divContainer{
	margin: 20px;
}

.dashboard{
	display: flex;
	justify-content: space-around;
	width: 100%;
	margin: auto;
}

.dashboard a{
	color: #898989;
	width: calc(100% / 5);
	padding: 20px;
	background-color: #fff;
	font-size: 25pt;
	text-align: center;
}

.dashboard p{
	color: #3279a7;
}

.dashboard a span{
	font-weight: bold;
	font-size: 14pt;
}

.containerPerfil{
	display: flex;
	justify-content: space-around;
	align-items: flex-start;
	flex-wrap: wrap;
}

.containerDataUser, .containerDataEmpresa{
	width: 500px;
	background-color: #fff;
	padding: 20px;
}

/********** ESTILO DE LO QUE SON LOS DATOS DE LA EMPRESA Y USUARIOS ***********/
.logoUser, .logoEmpresa{
	display:flex;
	justify-content: center;
	width: 200px;
	height: 200px;
	border-radius: 50%;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	-ms-border-radius: 50%;
	-o-border-radius: 50%;
	margin: 20px auto;
	padding: 25px;
	background: #e9e9e9;
}

.logoUser img, .logoEmpresa img{
	width: 100%;
	height: 100%;
}

.divDataUser{
	padding: 10px;
	margin: auto;
}

.divInfoSistema h4{
	background: #11B90A;
	padding: 5px 10px;
	color: #fff;
	border-radius: 3px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	-ms-border-radius: 3px;
	-o-border-radius: 3px;
	text-align: center;
	margin-bottom: 10px;
}

.divDataUser > div{
	display: flex;
}

.divDataUser label{
	width: 150px;
	margin: 0;
	margin-bottom: 10px;
	margin-top: 10px;
}

.divDataUser span{
	padding: 5px;
}

.divInfoSistema form{
	padding: 20px;
}

.divInfoSistema input{
	margin-bottom: 10px;
}

.divInfoSistema button{
	width: 100%;
}

/*********** ESTILO DE LAS ALERTAS DE CONTRASEÑA *************/
.alertChangePass{
	text-align: center;
	font-weight: bold;
}

/************ ESTILO DEL LISTADO DE VENTAS ************/
.form_search_date{
	padding: 10px;
	display: flex;
	justify-content: flex-start;
	align-items: center;
	border-radius: 10px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	-ms-border-radius: 10px;
	-o-border-radius: 10px;
	margin: 10px auto;
}
.form_search_date label{
	margin: 0 10px;
}
.form_search_date input{
	width: auto;
}
.form_search_date .btn_view{
	padding: 8px;
}
.btn_view{
	background-color: #1faac8;
	border: 0;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	-ms-border-radius: 5px;
	-o-border-radius: 5px;
	cursor: pointer;
	padding: 10px;
	margin: 0 3px;
	color: #fff;
}
.btn_reporte{
		background-color: #11B90A;
		border: 0;
		border-radius: 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		-ms-border-radius: 5px;
		-o-border-radius: 5px;
		cursor: pointer;
		padding: 10px;
		margin: 0 3px;
		color: #fff;
}
.div_acciones{
	display: flex;
	justify-content: center;
}
.totalfactura{
	display: flex;
	justify-content: space-between;
}
.pagada, .anulada{
	color: #fff;
	background: #60a756;
	text-align: center;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	-ms-border-radius: 5px;
	-o-border-radius: 5px;
	padding: 4px 15px;
}
.anulada{
	background: #f36a6a;
}
.inactive{
	background-color: #AAAAA4;
	color: #CCC;
	cursor: default;
}

/******** FORMATO DEL ESTILO DEL MODAL *********/
.modal{
	position: fixed;
	width: 100%;
	height: 100vh;
	background: rgba(0, 0, 0, 0.81);
	display: none;
}
.bodyModal{
	width: 100%;
	height: 100%;
	display: inline-flex;
	justify-content: center;
	align-items: center;
}
.modal h1{
	color: #0E725D;
	text-transform: uppercase;
}
.model h2{
	text-transform: uppercase;
	margin-top: 15px;
}
#form_add_product,#form_anular_factura{
	width: 420px;
	text-align: center;
}

/*BOTON DE ENVIAR EGRESO */
.wd302 input{
	position: relative;
	padding: 10px 15px;
	border-radius: 5px;
	outline: none;
	background: #358b13;
	cursor: pointer;
	color: #FFF;
	width: 400px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	-ms-border-radius: 5px;
	-o-border-radius: 5px;
	font-size: 14px;
}
.wd302 input:hover{
	background: #a2ad0b;
}

/********** COLOR DE LETRA DEL TOTAL DEL PANEL DE CONTROL *********/
.total{
	color: #6ac916;
}
.totale{
	color: #e63636;
}
.totals{
	color: #d4db02;
}

/*****************ESTILOS DE LAS CARDS *******************/
.container{
	padding: 170px;
	display: flex;
	margin: 10px auto;
	width: 100%;
	height: 430px;
	flex-wrap: wrap;
	justify-content: center;
}

.container .card{
	width: 300px;
	height: 430px;
	border-radius: 8px;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	-ms-border-radius: 8px;
	-o-border-radius: 8px;
	box-shadow: 0 2px 2px rgba(0, 0, 0, 0.2);
	overflow: hidden;
	margin: 10px;
	text-align: center;
	transition: all 0.25s;
	-webkit-transition: all 0.25s;
	-moz-transition: all 0.25s;
	-ms-transition: all 0.25s;
	-o-transition: all 0.25s;
	background-color: #c8c8c8;
}

.container .card:hover{
	transform: translateY(-15px);
	-webkit-transform: translateY(-15px);
	-moz-transform: translateY(-15px);
	-ms-transform: translateY(-15px);
	-o-transform: translateY(-15px);
	box-shadow: 0 12px 16px rgba(0, 0, 0, 0.2);
}

.container .card img{
	width: 300px;
	height: 280px;
	padding: 10px;
}

.container .card h4{
	font-size: 20px;
	padding: 30px;
	color: #1b6b1e;
}

.container .card a{
	font-size: 20px;	
	text-decoration: none;
	color: #FFF;
}

.container .card a:hover{
	color: #1e7111;
}
</style>
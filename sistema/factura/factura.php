<?php 
//print_r($factura);  
//exit;

include "../../conexion.php"; 
 $subtotal  = 0; 
 $iva    = 0; 
 $impuesto  = 0; 
 $tl_sniva   = 0; 
 $total   = 0; 

 ?> 
 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
 <meta charset="UTF-8"> 
 <title>Factura</title> 
</head>
<body> 
<?= $anulada ?> 
<div id="page_pdf"> 
<table id="factura_head" >
  <tr> 
   <td class="logo_factura" style="background-image:url(https://ws.net.pe/sistema/factura/img/ws.png); 
   background-repeat:no-repeat;background-size:120px 100px;width:100px;"> 
   </td>
   <td class="info_empresa"> 
    <?php 
     if($result_config > 0){ 
      $iva = $configuracion['confi_igv']; 
    ?> 
    <div> 
     <span class="titulo"><B><?php echo strtoupper($configuracion['confi_nombrecomer']) ?></span> 
     <p  class="h2"><B><?php echo $configuracion['confi_nombrelegal']; ?></p> 
     <p class="h2"><?php echo $configuracion['confi_direccion']; ?></p> 
     <br> 
     <p class="h2">Página web: www.website.com.pe</p> 
     <p class="h2">Teléfono: <?php echo $configuracion['confi_telefono']; ?></p> 
     <p class="h2">E-mail: <?php echo $configuracion['confi_correo']; ?></p> 
    </div> 
    <?php 
     } 
    ?> 
   </td> 
   <td class="info_factura"> 
    <div class="round1">
      <br> 
     <p class="numero"><strong>RUC: <?php echo $configuracion['confi_ndocumento'];?></strong></p>
     <br> 
     <p class="numero"><strong>N° <?php echo $factura['nombre'];?>:  WS000<?php echo  $factura['id_comprobante'] ?></strong></p>
    </div> 
   </td> 
  </tr> 
 </table> 
 <table id="factura_cliente"> 
  <tr> 
   <td class="info_cliente"> 
    <div class="round2"> 
       <ol class="cli"><label ><b>RAZÓN SOCIAL: </label><?php echo $factura['cli_nombre'];?></ol> 
       <ol class="cli1"><label ><b>RUC: </label><?php echo $factura['cli_documento']; ?></ol> 
       <ol class="cli2"><label ><b>DIRECCIÓN: </label><?php echo $factura['cli_direccion'];?></ol>
       <ol class="cli3"><label ><b>TELÉFONO: </label><?php echo $factura['cli_telefono'];?></ol><br> 
       <ol class="cli4"><label ><b>EMISIÓN: </label><?php echo $factura['fecha'] ?> <?php echo $factura['hora']; ?></ol>
       <ol class="cli5"><label ><b>MONEDA: </label>SOL(PEN)</ol>
       <ol class="cli6"><label ><b>FORMA DE PAGO: </label>CONTADO</ol>
    </div> 
   </td> 
 
  </tr> 
 </table> 
 
 <table id="factura_detalle"> 
   <thead> 
    <tr> 
     <th width="50px"></th> 
     <th class="textleft">Servicio</th> 
     <th class="textleft">Contrato (Meses)</th> 
     <!--<th class="textright" width="150px"></th> -->
     <th class="textright" width="150px"> Precio Unitario</th> 
    </tr> 
   </thead> 
   <tbody id="detalle_productos"> 
 
   <?php  
    if($result_detalle > 0){ 
     while ($row = mysqli_fetch_assoc($query_productos)){ 
    ?> 
    <tr> 
     <td class="textcenter"></td> 
     <td><?php echo $row['servi_nombre']; ?></td> 
     <td class="texth"><?php echo $row['tiempo']; ?></td>
     <!--<td class="textright"></td> -->
     <td class="textright"><?php echo $row['precio_total']; ?></td> 
    </tr> 
   <?php 
      $precio_total = $row['precio_total']; 
      $subtotal = round($subtotal + $precio_total, 2); 
     } 
    } 
 
    $impuesto  = round($subtotal * ($iva / 100), 2); 
    $tl_sniva  = round($subtotal - $impuesto,2 ); 
    $total   = round($tl_sniva + $impuesto,2); 
   ?> 
   </tbody> 
   <br> 
   <tfoot id="detalle_totales"> 
    <tr> 
     <td colspan="3" class="textright"><span>SUBTOTAL: </span></td> 
     <td class="textright"><span><?php echo $tl_sniva; ?></span></td> 
    </tr> 
    <tr> 
     <td colspan="3" class="textright"><span>IGV (<?php echo $iva; ?> %): </span></td> 
     <td class="textright"><span><?php echo $impuesto; ?></span></td> 
    </tr> 
    <tr> 
     <td colspan="3" class="textright"><span>IMPORTE TOTAL (S/): </span></td> 
     <td class="textright1"><span><b><?php echo $total; ?></span></td> 
    </tr> 
  </tfoot> 
 </table> 

 <div class="footer_factura"> 
    <p class="nota1">Vendido por:  <B><?php echo $factura['vendedor']; ?></p><br>
    <p class="nota">WEBSITE MKTD te recuerda pagar puntual, tienes 48 hrs para pagar o se le cobrará mora del 3% por día, puede pagar 
    mediante nuestros números de cuenta: 
    </p><br>
    <p class="nota">YAPE / PLIN / TUNKI: 979181211  
    </p> 
    <p class="nota">BANCO INTERBANK: 700-3001980275  
    </p>
    <p class="nota">BANCO BBVA: 0011-0348-0200346672-05  
    </p><BR><br>
    <p class="nota">Adelanta tu pago y obtén hasta 10% de descuento. 
    </p> 
    <h4 class="label_gracias">¡Gracias por su compra!</h4> 

  </div> 
</body>  
</html> 
<style> 
   *{ 
 margin: 0; 
 padding: 0; 
 box-sizing: border-box; 
} 
.texth{
  padding-left: 40px;

}
.titulo{
  font-size: 20pt;
}
.textright1{ 
 text-align: right; 
 font-size: 14pt;
} 
.nota1{
  text-align: left;
  right: 50px !important;
  font-size: 10pt;
  font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
}
.cli{
  text-align: left;
  padding-left: 32px;
  
}
.round2{
  font-size: 10pt;
}
.cli1{
  text-align: left;
 padding-left: 112px;

}
.cli2{
  text-align: left;
  padding-left: 62px;
}
.cli3{
  text-align: left;
  padding-left: 66px; 
}
.cli4{
  text-align: left;
  padding-left: 84px; 
}
.cli5{
  text-align: left;
  padding-left: 82px; 
}
.cli6{
  text-align: left;
  padding-left: 22px; 
}
.round1{
  width: 180px;
  height: 120px;
  padding: 15px;
  border: 3px  black solid
}

.numero{
  font-size: 14pt;
  font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
text-align: center;

}
.logo_factura{
 width: 28% !important;
}
.footer_factura
{ 
 padding-top: auto; 
 text-align: center;
} 
p, label, span, table{ 
 font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; 
 font-size: 11pt; 
} 
.h2{ 
 font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; 
 font-size: 10pt; 
} 
.p1{
  font-size: 25pt;
} 
#page_pdf{ 
 width: 90%; 
 margin: 30px auto 10px auto; 
} 
 
#factura_head, #factura_cliente{ 
 width: 100%; 
 margin-bottom: 10px; 
} 
#factura_detalle{
  width: 100%;
  height: 40%;
  margin: 20px auto 10px auto; 
}
 
.info_empresa{ 
 width: 80% !important; 
 text-align: left ; 
} 
.info_factura{
 width: 30%;
 text-align: center;
}  
.datos_cliente{ 
 width: 100%; 

} 
.datos_cliente tr td{ 
 width: 50%; 
} 
.datos_cliente{ 
 padding: 10px 10px 0 10px; 
} 
.datos_cliente label{ 
 width: 75px; 
 display: inline-block; 
} 
.datos_cliente p{ 
 display: inline-block; 
} 
 
.textright{ 
 text-align: right; 
} 
.textleft{ 
 text-align: left; 
} 
.textcenter{ 
 text-align: center; 
} 

.round p{ 
 padding: 0 15px; 
} 
 
#factura_detalle{ 
 border-collapse: collapse; 
} 
#factura_detalle thead th{ 
 background: #11B90A; 
 color: #FFF; 
 padding: 5px; 
} 
#detalle_productos tr:nth-child(even) { 
    background: #ededed; 
} 
#detalle_totales span{ 
 font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; 
} 
.nota{ 
 font-size: 8pt; 
} 
.label_gracias{ 
 font-family: verdana; 
 font-weight: bold; 
 font-style: italic; 
 text-align: center; 
 margin-top: 20px; 
} 
.anulada{ 
 position: absolute; 
 left: 50%; 
 top: 50%; 
 transform: translateX(-50%) translateY(-50%); 
} 
  </style>


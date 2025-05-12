<nav> 
 <ul> 
   
 
  <li><a class="estilo2"><i></i></a></li> 
  <li><a class="estilo2"><i></i></a></li> 
  <li><a class="estilo2"><i></i></a></li> 
  <li><a class="estilo2"><i></i></a></li> 
  <img width="155" height="155"  src="./img/admin-ajax.png" class="icono"> 
 
 
  <li><a class="estilo" href="index.php"><i class="fas fa-home"></i> Inicio</a></li> 
 
  <li class="principal"> 
   <a class="estilo" href="nueva_venta.php"><i class="fas fa-plus"></i> Nueva Venta</a> 
   <ul> 
    <!--<li><a href="nueva_venta.php"><i class="fas fa-plus"></i> Nueva Venta</a></li>--> 
    <!--<li><a href="#"><i class="far fa-newspaper"></i> Listado de Ingresos</a></li>--> 
   </ul> 
  </li> 
 
  <?php if ($_SESSION['rol'] != 4) { ?> 
   <li class="principal"> 
    <a class="estilo" href="botones_egreso.php"><i class="fas fa-plus"></i> EMITIR EGRESOS </a>
    <ul> 
     <!--<li><a href="nuevo_egreso.php"><i class="fas fa-plus"></i> Nuevo Egreso</a></li>--> 
     <!--<li><a href="#"><i class="far fa-newspaper"></i> Lista de Egresos</a></li>--> 
    </ul> 
   </li> 
  <?php  } ?> 
 
 
  <!--<li><a href="registro_servicio.php"><i class="fas fa-plus"></i> Nuevo Servicio</a></li>--> 
 
 
 
  <li class="principal"> 
   <a class="estilo" href="ventas.php"><i class="far fa-file-alt"></i> Listado Ingresos</a> 
   <ul> 
    <?php if ($_SESSION['rol'] != 4) { ?> 
     <li><a class="estilo" href="listado_egreso.php"><i class="far fa-newspaper"></i> Listado de los Egresos</a></li> 
     <li><a class="estilo" href="listar_servicio.php"><i class="fas fa-cubes"></i> Lista de Servicios</a></li> 
    <?php  } ?> 
   </ul> 
  </li> 
 
  <li class="principal"> 
   <a class="estilo" href="listar_cliente.php"><i class="fas fa-users"></i> Clientes</a> 
   <ul> 
    <li><a class="estilo" href="registro_cliente.php"><i class="fa fa-user-plus"></i> Nuevo Cliente</a></li> 
   </ul> 
  </li> 
 
  <li class="principal"> 
   <?php if ($_SESSION['rol'] != 3 && $_SESSION['rol'] != 4) { ?> 
    <a class="estilo2" href="listar_usuario.php"><i class="fas fa-users"></i> Usuarios</a> 
    <ul> 
     <li><a class="estilo" href="registro_usuario.php"><i class="fa fa-user-plus"></i> Nuevo Usuario</a></li> 
    </ul> 
  </li> 
 <?php  } ?> 
 </ul> 
 
</nav> 
<br><br>
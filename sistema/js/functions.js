//INICIAR TODOS LOS FUNCIONAMIENTOS
$(document).ready(function () {
  //ELIMINAR SERVICIOS
  $(".del_product").click(function (e) {
    e.preventDefault();
    // accedes a "este" atributo
    var producto = $(this).attr("product");
    var action = "infoProducto";

    $.ajax({
      // metodo jquery para dirigir otro archivo y traer datos hacia este mismo function
      url: "ajax.php",
      type: "POST",
      async: true,
      // reducí el código para acceder al mismo nombre de clave
      data: { action, producto },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);

          $(".bodyModal").html(
            '<form action="" method="form_add_product" id="form_add_product" onsubmit="event.preventDefault()"'
          );
          '<h1><i class="fas fa-cubes" style="font-size:45pt;"></i> <br> Eliminar Producto</h1>' +
            "<h2>¿Esta seguro de eliminar el siguiente registro?</h2>" +
            '<h2 class="nameProducto">' +
            info.descripcion +
            "</h2><br>";
        }
      },
    });
  });

  //ACTIVAR CAMPOS PARA REGISTRAR CLIENTES
  $(".btn_new_cliente").click(function (e) {
    e.preventDefault();
    $("#nom_cliente").removeAttr("disabled");
    $("#tel_cliente").removeAttr("disabled");
    $("#dire_cliente").removeAttr("disabled");

    $("#div_registro_cliente").slideDown();
  });

  //BUSCAR CLIENTE
  $("#documento_cliente").keyup(function (e) {
    e.preventDefault();

    // obtienes valor de la respuesta el teclado
    var cl = $(this).val();
    var action = "searchCliente";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action, cliente: cl },

      success: function (response) {
        if (response == 0) {
          $("#idcliente").val("");
          $("#nom_cliente").val("");
          $("#tel_cliente").val("");
          $("#dire_cliente").val("");

          //MOSTRAR BOTON AGREGAR
          $(".btn_new_cliente").slideDown();
        } else {
          var data = $.parseJSON(response);
          // se presta el id para colocar como valor el objeto.propiedad
          $("#idcliente").val(data.id_cliente);
          $("#nom_cliente").val(data.cli_nombre);
          $("#tel_cliente").val(data.cli_telefono);
          $("#dire_cliente").val(data.cli_direccion);

          //OCULTAR BOTON AGREGAR
          $(".btn_new_cliente").slideUp();

          //BLOQUEO DE CAMPOS
          $("#nom_cliente").attr("disabled", "disabled");
          $("#tel_cliente").attr("disabled", "disabled");
          $("#dire_cliente").attr("disabled", "disabled");

          //OCULTAR BOTON GUARDAR
          $("#div_registro_cliente").slideUp();
        }
      },
      error: function (error) {},
    });
  });

  //CREAR CLIENTE DESDE VENTAS
  $("#form_new_cliente_venta").submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: $("#form_new_cliente_venta").serialize(),

      success: function (response) {
        if (response != "error") {
          //AGREGAR ID A INPUT HIDEN
          $("#idcliente").val(response);

          //BLOQUEO DE CAMPOS
          $("#nom_cliente").attr("disabled", "disabled");
          $("#tel_cliente").attr("disabled", "disabled");
          $("#dire_cliente").attr("disabled", "disabled");

          //OCULTAR BOTON AGREGAR
          $(".btn_new_cliente").slideUp();

          //OCULTAR BOTON GUARDAR´
          $("#div_registro_cliente").slideUp();
        }
      },
      error: function (error) {},
    });
  });

  //BUSCAR PRODUCTOS

  $("#txt_cod_producto").keypress(function (e) {
    var key = e.which;
    if (key == 13) {
      // the enter key code
      var producto = $(this).val();
      var action = "infoProducto";

      if (producto == "") {
        producto = -1;
      }
      $.ajax({
        url: "ajax.php",
        type: "POST",
        async: true,
        data: { action, producto },

        success: function (response) {
          if (response != "error") {
            $("#htmlServicios").html(response);
          } else {
            $("#txt_descripcion").html("-");
            $("#txt_meses").html("-");
            $("#txt_precio_total").html("0.00");

            //OCULTAR AGREGAR
            $("#add_product_venta").slideUp();
          }
        },
        error: function (error) {},
      });
    }
  });
  //AGREGAR PRODUCTO AL DETALLE
  function addProductoDetalle(cod_servicio) {
    //AGREGAR VALORES DEL TXT DE NUEVA VENTA A UNA VARIABLE
    var producto = cod_servicio;
    var action = "addProductoDetalle";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action, producto },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);
          $("#detalle_venta").html(info.detalle);
          $("#detalle_totales").html(info.totales);

          $("#txt_cod_producto").val("");
          $("#txt_descripcion").html("-");
          $("#txt_meses").html("-");
          $("#txt_precio_total").html("0.00");

          //OCULTAR BOTON AGREGAR
          $("#add_product_venta").slideUp();
        } else {
          console.log("NO DATA");
        }
        viewProcesar();
      },
      error: function (error) {},
    });
  }

  //FUNCIONAMIENTO DEL BOTON ANULAR VENTAS
  $("#btn_anular_venta").click(function (e) {
    e.preventDefault();

    var rows = $("#detalle_venta tr").length;
    if (rows > 0) {
      var action = "anularVenta";

      $.ajax({
        url: "ajax.php",
        type: "POST",
        async: true,
        data: { action },

        success: function (response) {
          if (response != "error") {
            // permite recargar la pagina cuando if sea verdadero
            location.reload();
          }
        },
        error: function (error) {},
      });
    }
  });

  //FACTURAR VENTA
  $("#btn_facturar_venta").click(function (e) {
    e.preventDefault();

    // detecta el numero de elementos
    var rows = $("#detalle_venta tr").length;

    if (rows > 0) {
      var action = "procesarVenta";
      var codcliente = $("#idcliente").val();
      var codComprobante = $("#tc").val();
      //var anexo = $('#comentario').val();

      $.ajax({
        url: "ajax.php",
        type: "POST",
        async: true,
        data: {
          action: action,
          codcliente: codcliente,
          codComprobante: codComprobante,
        },

        success: function (response) {
          if (response != "error") {
            var info = JSON.parse(response);
            //console.log(info);
            generarPDF(info.id_cliente, info.id_comprobante);
            location.reload();
          } else {
            console.log("NO DATA");
          }
        },
        error: function (error) {},
      });
    }
  });

  //MOSTRARDE MODAL DE DETALLE DE CLIENTES
  $(".mostrar_direccion_cliente").click(function (e) {
    e.preventDefault();

    var idcliente = $(this).attr("fa");
    var action = "infoDireccionCliente";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action, idcliente },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);

          $(".bodyModal").html(`
							<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault();">
									<h1><i class="far fa-newspaper" style="font-size: 45pt;"></i><br> DIRECCION</h1><br>
									<p><strong>${info.cli_direccion}</strong></p><br>
									<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>
							</form>
					`);
        }
      },
    });

    $(".modal").fadeIn();
  });

  //CAMBIAR CONTRASEÑA
  $(".newPass").keyup( ()=> {
    validPass();
  });

  //FORMULARIO DE CAMBIAR CONTRASEÑA
  $("#frmChangePass").submit(function (e) {
    e.preventDefault();

    var passActual = $("#txtPassUser").val();
    var passNuevo = $("#txtNewPassUser").val();
    var confiNuevo = $("#txtPassConfirm").val();
    var action = "changePassword";

    if (passNuevo != confiNuevo) {
      $(".alertChangePass").html(
        '<p style="color: red;">Las contraseñas no son iguales</p>'
      );
      $(".alertChangePass").slideDown();
      return false;
    }
    //NIVEL DE SEGURIDAD
    if (passNuevo < 6) {
      $(".alertChangePass").html(
        '<p style="color: red;">La nueva contraseña debe tener como minimo 6 caracteres</p>'
      );
      $(".alertChangePass").slideDown();
      return false;
    }
    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action: action, passActual: passActual, passNuevo: passNuevo },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);
          if (info.cod == "00") {
            $(".alertChangePass").html(
              '<p style="color: green;">' + info.msg + "</p>"
            );
            $("#frmChangePass")[0].reset();
          } else {
            $(".alertChangePass").html(
              '<p style="color: red;">' + info.msg + "</p>"
            );
          }
          $(".alertChangePass").slideDown();
        }
      },
      error: function (error) {},
    });
  });

  //ACTUALIZAR LOS DATOS DE LA EMPRESA
  $("#frmEmpresa").submit(function (e) {
    e.preventDefault();

    var ruc = $("#txtRuc").val();
    var nombrelegal = $("#txtNombreLegal").val();
    var nombrecomercial = $("#txtNombreComercial").val();
    var telefono = $("#txtTelefono").val();
    var correo = $("#txtCorreo").val();
    var direccion = $("#txtDireccion").val();
    var igv = $("#txtIGV ").val();

    if (
      ruc == "" ||
      nombrelegal == "" ||
      nombrecomercial == "" ||
      telefono == "" ||
      correo == "" ||
      direccion == "" ||
      igv == ""
    ) {
      $(".alertFormEmpresa").html(
        '<p style="color: red;">Todos los campos son obligatorios.</p>'
      );
      $(".alertFormEmpresa").slideDown();
      return false;
    }

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: $("#frmEmpresa").serialize(),
      beforeSend: function () {
        $(".alertFormEmpresa").slideUp();
        $(".alertFormEmpresa").html("");
        $("#frmEmpresa input").attr("disabled", "disabled");
      },
      success: function (response) {
        var info = JSON.parse(response);
        if (info.cod == "00") {
          $(".alertFormEmpresa").html(
            '<p style="color: #23922d;">' + info.msg + "</p>"
          );
          $(".alertFormEmpresa").slideDown();
        } else {
          $(".alertFormEmpresa").html(
            '<p style="color: red;">' + info.msg + "</p>"
          );
        }
        $(".alertFormEmpresa").slideDown();
        $("#frmEmpresa input").removeAttr("disabled");
      },
      error: function (error) {},
    });
  });

  //MOSTRARDE MODAL DE ELIMINAR FACTURA
  $(".anular_factura").click(function (e) {
    e.preventDefault();

    var nofactura = $(this).attr("fa");
    var action = "infoFactura";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action: action, nofactura: nofactura },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);

          $(".bodyModal").html(
            '<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault(); anularFactura();">' +
              '<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Anular Factura</h1><br>' +
              "<p>¿Realmente desea Anular la factura?</p>" +
              "<p><strong>Nº. " +
              info.id_comprobante +
              "</strong></p>" +
              "<p><strong>Monto. " +
              info.com_totalfactura +
              "</strong></p>" +
              "<p><strong>Fecha. " +
              info.com_fechaemi +
              "</strong></p>" +
              '<input type="hidden" name="action" value="anularFactura">' +
              '<input type="hidden" name="no_factura" id="no_factura" value="' +
              info.id_comprobante +
              '" required>' +
              '<div class="alert alertAddProduct"></div>' +
              '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Anular</button>' +
              '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>' +
              "</form>"
          );
        }
      },
    });

    $(".modal").fadeIn();
  });

  //MOSTRARDE MODAL DE DETALLE DE EGRESOS
  $(".mostrar_detalle").click(function (e) {
    e.preventDefault();

    var idegreso = $(this).attr("fa");
    var action = "infoDetalleEgreso_v1";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action: action, idegreso: idegreso },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);

          $(".bodyModal").html(
            '<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault();">' +
              '<h1><i class="far fa-newspaper" style="font-size: 45pt;"></i> <br> DETALLE </h1><br>' +
              "<p><strong>" +
              info.detalle +
              "</strong></p>" +
              "<br>" +
              '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>' +
              "</form>"
          );
        }
      },
    });

    $(".modal").fadeIn();
  });

  //MOSTRARDE MODAL DE DETALLE DE EGRESOS
  $(".mostrar_direccion").click(function (e) {
    e.preventDefault();

    var idegreso = $(this).attr("fa");
    var action = "infoDireccionEgreso";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action: action, idegreso: idegreso },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);

          $(".bodyModal").html(
            '<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault();">' +
              '<h1><i class="far fa-newspaper" style="font-size: 45pt;"></i> <br> DIRECCION</h1><br>' +
              "<p><strong>" +
              info.direccion +
              "</strong></p>" +
              "<br>" +
              '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>' +
              "</form>"
          );
        }
      },
    });

    $(".modal").fadeIn();
  });

  //MOSTRARDE MODAL DE DETALLE DE EGRESOS FIJOS
  $(".mostrar_detalle").click(function (e) {
    e.preventDefault();

    var fijo = $(this).attr("fa");
    var action = "infoDetalleEgreso1";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action: action, fijo: fijo },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);

          $(".bodyModal").html(
            '<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault();">' +
              '<h1><i class="far fa-newspaper" style="font-size: 45pt;"></i> <br> DETALLE</h1><br>' +
              "<p><strong>" +
              info.fj_descripcion +
              "</strong></p>" +
              "<br>" +
              '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>' +
              "</form>"
          );
        }
      },
    });

    $(".modal").fadeIn();
  });

  //MOSTRARDE MODAL DE DETALLE DE EGRESOS variable
  $(".mostrar_detalle_variable").click(function (e) {
    e.preventDefault();

    var variable = $(this).attr("fa");
    var action = "infoDetalleEgresoV";

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: { action: action, variable: variable },

      success: function (response) {
        if (response != "error") {
          var info = JSON.parse(response);

          $(".bodyModal").html(
            '<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault();">' +
              '<h1><i class="far fa-newspaper" style="font-size: 45pt;"></i> <br> DETALLE</h1><br>' +
              "<p><strong>" +
              info.descripcion +
              "</strong></p>" +
              "<br>" +
              '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>' +
              "</form>"
          );
        }
      },
    });

    $(".modal").fadeIn();
  });
  //MOSTRAR EL PDF
  $(".view_factura").click(function (e) {
    e.preventDefault();

    var codCliente = $(this).attr("cl");
    var noFactura = $(this).attr("f");

    generarPDF(codCliente, noFactura);
  });

  //CREAR USUARIO DESDE NUEVO EGRESO
  $("#form_new_usuario_venta").submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: "ajax.php",
      type: "POST",
      async: true,
      data: $("#form_new_usuario_venta").serialize(),

      success: function (response) {
        if (response != "error") {
          //AGREGAR ID A INPUT HIDEN
          $("#idusuario").val(response);

          //BLOQUEO DE CAMPOS
          $("#nom_usuario").attr("disabled", "disabled");
          $("#tel_usuario").attr("disabled", "disabled");
          $("#dire_usuario").attr("disabled", "disabled");

          //OCULTAR BOTON AGREGAR
          $(".btn_new_usuario").slideUp();

          //OCULTAR BOTON GUARDAR´
          $("#div_registro_usuario").slideUp();
        }
      },
      error: function (error) {},
    });
  });

  //FUNCIONAMIENTO DEL BOTON ANULAR VENTAS EN EGRESOS
  $("#btn_anular_egreso").click(function (e) {
    e.preventDefault();

    var rows = $("#detalle_venta tr").length;
    if (rows > 0) {
      var action = "anularVentas";

      $.ajax({
        url: "ajax.php",
        type: "POST",
        async: true,
        data: { action: action },

        success: function (response) {
          if (response != "error") {
            location.reload();
          }
        },
        error: function (error) {},
      });
    }
  });

  //FACTURAR VENTA EGRESOS
  $("#btn_facturar_egreso").click(function (e) {
    e.preventDefault();

    var rows = $("#detalle_venta tr").length;

    if (rows > 0) {
      var action = "procesarVentaEgreso";
      var codusuario = $("#idusuario").val();

      $.ajax({
        url: "ajax.php",
        type: "POST",
        async: true,
        data: { action: action, codusuario: codusuario },

        success: function (response) {
          if (response != "error") {
            var infos = JSON.parse(response);
            console.log(infos);

            generarPDFEgreso(infos.id_usu, infos.id_externo);
            location.reload();
          } else {
            console.log("NO DATA");
          }
        },
        error: function (error) {},
      });
    }
  });

  //REGISTRAR FORMULARIO DE EGRESOS
});
//ANULAR FACTURA
function anularFactura() {
  var noFactura = $("#no_factura").val();
  var action = "anularFactura";

  $.ajax({
    url: "ajax.php",
    type: "POST",
    async: true,
    data: { action: action, noFactura: noFactura },

    success: function (response) {
      if (response == "error") {
        $(".alertAddProduct").html(
          '<p style="color: red;"> Error al anular la Factura.</p>'
        );
      } else {
        $("#row_" + noFactura + " .estado").html(
          '<span class="anulada">Anulada</span>'
        );
        $("#form_anular_factura .btn_ok").remove();
        $("#row_" + noFactura + " .div_factura").html(
          '<button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>'
        );
        $(".alertAddProduct").html("<p>Factura Anulada.</p>");
      }
    },
    error: function (error) {},
  });
}

//CERRAR MODAL
function closeModal() {
  $(".modal").fadeOut();
}
//VALIDACION DE LA NUEVAS CONTRASEÑAS
function validPass() {
  var passNuevo = $("#txtNewPassUser").val();
  var confiNuevo = $("#txtPassConfirm").val();
  if (passNuevo != confiNuevo) {
    $(".alertChangePass").html(
      '<p style="color: red;">Las contraseñas no son iguales</p>'
    );
    $(".alertChangePass").slideDown();
    return false;
  }
  //NIVEL DE SEGURIDAD
  if (passNuevo < 6) {
    $(".alertChangePass").html(
      '<p style="color: red;">La nueva contraseña debe tener como minimo 6 caracteres</p>'
    );
    $(".alertChangePass").slideDown();
    return false;
  }
  $(".alertChangePass").html("");
  $(".alertChangePass").slideUp();
}

//GENERAR EL PDF
function generarPDF(cliente, factura) {
  //DEFINE EL ANCHO Y ALTO DE LA VENTANA A MOSTRAR
  var ancho = 1000;
  var alto = 800;

  //CALCULAR PSICION X,Y PARA CENTRAR LA VENTANA
  var x = parseInt(window.screen.width / 2 - ancho / 2);
  var y = parseInt(window.screen.height / 2 - alto / 2);

  $url = "factura/generaFactura.php?cl=" + cliente + "&f=" + factura;
  window.open(
    $url,
    "Factura",
    "left=" +
      x +
      ",top=" +
      y +
      ",height=" +
      alto +
      ",width=" +
      ancho +
      ",scrollbar=si,location=no,resizable=si,menubar=no"
  );
}

//ELIMINAR DETALLE_TEMP
function del_product_detalle(id_temp) {
  var action = "delProductoDetalle";
  var id_detalle = id_temp;

  $.ajax({
    url: "ajax.php",
    type: "POST",
    async: true,
    data: { action: action, id_detalle: id_detalle },

    success: function (response) {
      if (response != "error") {
        var info = JSON.parse(response);
        $("#detalle_venta").html(info.detalle);
        $("#detalle_totales").html(info.totales);

        $("#txt_cod_producto").val("");
        $("#txt_descripcion").html("-");
        $("#txt_meses").html("-");
        $("#txt_precio_total").html("0.00");

        //OCULTAR BOTON AGREGAR
        $("#add_product_venta").slideUp();
      } else {
        $("#detalle_venta").html("");
        $("#detalle_totales").html("");
      }
      viewProcesar();
    },
    error: function (error) {},
  });
}
//MOSTRAR/OCULTAR EL BOTON DE PROCESAR
function viewProcesar() {
  if ($("#detalle_venta tr").length > 0) {
    $("#btn_facturar_venta").show();
  } else {
    $("#btn_facturar_venta").hide();
  }
}

//FUNCION PARA QUE NO SE BORRE EL REGISTRO SI ES QUE NOS MOVEMOS DE PESTAÑA TENIENDO INFO EN NUEVA VENTA
function serchForDetalle(id) {
  var action = "serchForDetalle";
  var user = id;

  $.ajax({
    url: "ajax.php",
    type: "POST",
    async: true,
    data: { action: action, user: user },

    success: function (response) {
      if (response != "error") {
        var info = JSON.parse(response);
        $("#detalle_venta").html(info.detalle);
        $("#detalle_totales").html(info.totales);
      } else {
        console.log("NO DATA");
      }
      viewProcesar();
    },
    error: function (error) {},
  });
}

//AGREGAR PRODUCTO AL DETALLE
function addProductoDetalle(cod_servicio) {
  //AGREGAR VALORES DEL TXT DE NUEVA VENTA A UNA VARIABLE
  var producto = cod_servicio;
  var action = "addProductoDetalle";

  $.ajax({
    url: "ajax.php",
    type: "POST",
    async: true,
    data: { action: action, producto: producto },

    success: function (response) {
      if (response != "error") {
        var info = JSON.parse(response);
        $("#detalle_venta").html(info.detalle);
        $("#detalle_totales").html(info.totales);

        $("#txt_cod_producto").val("");
        $("#txt_descripcion").html("-");
        $("#txt_meses").html("-");
        $("#txt_precio_total").html("0.00");

        //OCULTAR BOTON AGREGAR
        $("#add_product_venta").slideUp();
      } else {
        console.log("NO DATA");
      }
      viewProcesar();
    },
    error: function (error) {},
  });
}

//PARA EL LOGIN
const inputs = document.querySelectorAll(".input");

function addcl() {
  let parent = this.parentNode.parentNode;
  parent.classList.add("focus");
}

function remcl() {
  let parent = this.parentNode.parentNode;
  if (this.value == "") {
    parent.classList.remove("focus");
  }
}

inputs.forEach((input) => {
  input.addEventListener("focus", addcl);
  input.addEventListener("blur", remcl);
});

function reporteIngresosPDF() {
  fechaIni = $("#fecha_de").val();
  fechaFin = $("#fecha_a").val();

  if (fechaIni != "" && fechaFin != "") {
    $.ajax({
      url: "factura/generaReporteIngreso.php",
      type: "GET",
      async: true,
      data: {
        fechaIni: fechaIni,
        fechaFin: fechaFin,
      },
      success: function (response) {
        window.open("factura/reportesPDF/reporteIngreso.pdf", "_blank");
      },
      error: function (error) {},
    });
  } else {
    alert("Selecione fechas");
  }
}

function reporteEgresosPDF() {
  fechaIni = $("#fecha_de").val();
  fechaFin = $("#fecha_a").val();

  if (fechaIni != "" && fechaFin != "") {
    $.ajax({
      url: "factura/generaReporteEgreso.php",
      type: "POST",
      async: true,
      data: {
        fechaIni: fechaIni,
        fechaFin: fechaFin,
      },
      success: function (response) {
        window.open("factura/reportesPDF/NuevoEgreso.pdf", "_blank");
      },
      error: function (error) {},
    });
  } else {
    alert("Selecione fechas");
  }
}

function reporteEgresosPerPDF() {
  fechaIni = $("#fecha_de").val();
  fechaFin = $("#fecha_a").val();

  if (fechaIni != "" && fechaFin != "") {
    $.ajax({
      url: "factura/generaReporteEgresoPersonal.php",
      type: "POST",
      async: true,
      data: {
        fechaIni: fechaIni,
        fechaFin: fechaFin,
      },
      success: function (response) {
        window.open("factura/reportesPDF/reportePersonal.pdf", "_blank");
      },
      error: function (error) {},
    });
  } else {
    alert("Selecione fechas");
  }
}

function reporteEgresoFijoPDF() {
  fechaIni = $("#fecha_de").val();
  fechaFin = $("#fecha_a").val();

  if (fechaIni != "" && fechaFin != "") {
    $.ajax({
      url: "factura/generaReporteEgresoCosto.php",
      type: "POST",
      async: true,
      data: {
        fechaIni: fechaIni,
        fechaFin: fechaFin,
      },
      success: function (response) {
        window.open("factura/reportesPDF/reporteCosto.pdf", "_blank");
      },
      error: function (error) {},
    });
  } else {
    alert("Selecione fechas");
  }
}

function reporteEgresosVariablePDF() {
  fechaIni = $("#fecha_de").val();
  fechaFin = $("#fecha_a").val();

  if (fechaIni != "" && fechaFin != "") {
    $.ajax({
      url: "factura/generaReporteEgresoVariable.php",
      type: "POST",
      async: true,
      data: {
        fechaIni: fechaIni,
        fechaFin: fechaFin,
      },
      success: function (response) {
        window.open("factura/reportesPDF/reporteVariable.pdf", "_blank");
      },
      error: function (error) {},
    });
  } else {
    alert("Selecione fechas");
  }
}

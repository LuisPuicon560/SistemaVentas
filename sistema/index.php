<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<link rel="icon" type="icon" href="../sistema/img/logoempresa.png">
	<title>Sistema Ventas</title>
</head>

<body>
	<?php
	include "includes/header.php";
	include "../conexion.php";

	//DATOS DE LA EMPRESA 
	$ruc = '';
	$nombrelegal = '';
	$nombrecomercial = '';
	$telefono = '';
	$correo = '';
	$direccion = '';
	$igv = '';

	$query_empresa = mysqli_query($conexion, "SELECT * FROM configuracion");
	$row_empresa = mysqli_num_rows($query_empresa);
	if ($row_empresa > 0) {
		while ($arrInfoEmpresa = mysqli_fetch_assoc($query_empresa)) {
			$ruc = $arrInfoEmpresa['confi_ndocumento'];
			$nombrelegal = $arrInfoEmpresa['confi_nombrelegal'];
			$nombrecomercial = $arrInfoEmpresa['confi_nombrecomer'];
			$telefono = $arrInfoEmpresa['confi_telefono'];
			$correo = $arrInfoEmpresa['confi_correo'];
			$direccion = $arrInfoEmpresa['confi_direccion'];
			$igv = $arrInfoEmpresa['confi_igv'];
		}
	}

	//LLAMAR EL PROCEDIMIENTO ALMACENADO (NOS FALTA COLOCAR LO QUE ES EL SELECT COUNT Y SELECT DE VENTAS Y EGRESOS EN PHPADMIN)
	$query_dash = mysqli_query($conexion, "CALL dataDashboard();");
	$result_das = mysqli_num_rows($query_dash);
	if ($result_das > 0) {
		$data_dash = mysqli_fetch_assoc($query_dash);
		mysqli_close($conexion);
	}
	print_r($data_dash);
	?>
	<br><br>
	<section id="container">
		<div class="divContainer">
			<div>
				<h1 class="tittlePanelControl">Panel de Control</h1>
			</div>
			<div class="dashboard">
				<?php if ($_SESSION['rol'] != 3 && $_SESSION['rol'] != 4) { ?>
					<a href="listar_usuario.php">
						<i class="fas fa-users"></i>
						<p>

							<strong>Usuarios</strong><br>
							<span><?= $data_dash['usuarios']; ?></span>
						</p>
					</a>
				<?php  } ?>
				<a href="listar_cliente.php">
					<i class="fas fa-user"></i>
					<p>
						<strong>Clientes</strong><br>
						<span><?= $data_dash['clientes']; ?></span>
					</p>
				</a>
				<a href="listar_servicio.php">
					<i class="fas fa-cubes"></i>
					<p>
						<strong>Servicios</strong><br>
						<span><?= $data_dash['servicios']; ?></span>
					</p>
				</a>

				<a href="ventas.php">
					<i class="fas fa-file-alt"></i>
					<p>
						<strong>Ventas al Dia</strong><br>
						<span><?= $data_dash['ventas']; ?></span>
					</p>
				</a>
				<a href="egresos.php">
					<i class="fas fa-file-alt"></i>
					<p>
						<strong>Egresos al Dia</strong><br>
						<span><?= $data_dash['egresos']; ?></span>
					</p>
				</a>
				<a href="ventas.php">
					<i class="fas fa-dollar-sign"></i>
					<p>
						<strong>Total de Ingresos Diarias</strong><br>
						<span class="total"><?= $data_dash['totalfactura']; ?></span>
					</p>
				</a>
				<a href="egresos.php">
					<i class="fas fa-dollar-sign"></i>
					<p>
						<strong>Total de Egresos Diarias</strong><br>
						<span class="totale"><?= $data_dash['totalegreso']; ?></span>
					</p>
				</a>
				<a href="#">
					<i class="fas fa-dollar-sign"></i>
					<p>
						<strong>Estado de perdidas y ganancias</strong><br>
						<?php
						$som = round($data_dash['totalfactura'] - ($data_dash['totalegreso'] + $data_dash['totalfijo'] + $data_dash['totalvariable'] + $data_dash['totalpersonal']), 2);
						?>
						<span class="totals"><?php echo $som; ?></span>
					</p>
				</a>
				<a href="registro_servicio.php">
					<i class="fas fa-plus"></i>
					<p>

						<strong>Nuevo Servicio</strong><br>
						<span></span>
					</p>
				</a>
			</div>
		</div>
		<!--******** INFORMACION DE LA CONFIGURACION DEL SISTEMA Y DEL USUARIO **********-->
		<div class="divInfoSistema">
			<div>
				<h1 class="tittlePanelControl">Configuracion</h1>
			</div>
			<div class="containerPerfil">
				<div class="containerDataUser">
					<div class="logoUser">
						<img src="img/users.png">
					</div>
					<div class="divDataUser">
						<h4>INFORMACION PERSONAL</h4>
						<div>
							<label>Nombre:</label> <span><?= $_SESSION['nombre']; ?></span>
						</div>
						<div>
							<label>Correo:</label> <span><?= $_SESSION['email']; ?></span>
						</div><br>
						<h4>DATOS DEL USUARIO</h4>
						<div>
							<label>Rol:</label> <span><?= $_SESSION['nomrol']; ?></span>
						</div>
						<div>
							<label>Usuario:</label> <span><?= $_SESSION['user']; ?></span>
						</div><br>
						<?php if ($_SESSION['rol'] != 3 && $_SESSION['rol'] != 4 && $_SESSION['rol'] != 2) { ?>

							<h4>CAMBIAR CONTRASEÑA</h4><br>
							<form action="" method="post" name="frmChangePass" id="frmChangePass">
								<div>
									<input type="password" name="txtPassUser" id="txtPassUser" placeholder="Contraseña actual" required><br>
								</div>
								<div>
									<input class="newPass" type="password" name="txtNewPassUser" id="txtNewPassUser" placeholder="Nueva Contraseña" required><br>
								</div>
								<div>
									<input class="newPass" type="password" name="txtPassConfirm" id="txtPassConfirm" placeholder="Confirmar Contraseña" required>
								</div>
								<div class="alertChangePass" style="display: none;">

								</div>
								<div>
									<button type="submit" class="btn_save btnChangePass"><i class="fas fa-key"></i> Cambiar Contraseña</button>
								</div>
							</form>
						<?php  } ?>
					</div>
				</div>

				<?php if ($_SESSION['rol'] != 3 && $_SESSION['rol'] != 4) { ?>

					<div class="containerDataEmpresa">
						<div class="logoEmpresa">
							<img src="img/logoempresa.png">
						</div>
						<h4>DATOS DE LA EMPRESA</h4><br>

						<form action="" method="post" name="frmEmpresa" id="frmEmpresa">
							<input type="hidden" name="action" value="updateDataEmpresa">
							<div>
								<label>RUC: </label><input type="text" name="txtRuc" id="txtRuc" placeholder="RUC de la Empresa" value="<?= $ruc; ?>" required>
							</div>
							<div>
								<label>Nombre Legal: </label><input type="text" name="txtNombreLegal" id="txtNombreLegal" placeholder="Nombre Legal de la Empresa" value="<?= $nombrelegal; ?>" required>
							</div>
							<div>
								<label>Nombre Comercial: </label><input type="text" name="txtNombreComercial" id="txtNombreComercial" placeholder="Nombre Comercial de la Empresa" value="<?= $nombrecomercial; ?>" required>
							</div>
							<div>
								<label>Telefono: </label><input type="text" name="txtTelefono" id="txtTelefono" placeholder="Telefono de la Empresa" value="<?= $telefono; ?>" required>
							</div>
							<div>
								<label>Correo Electronico: </label><input type="email" name="txtCorreo" id="txtCorreo" placeholder="Correo de la Empresa" value="<?= $correo; ?>" required>
							</div>
							<div>
								<label>Direccion: </label><input type="text" name="txtDireccion" id="txtDireccion" placeholder="Direccion de la Empresa" value="<?= $direccion; ?>" required>
							</div>
							<div>
								<label>IGV (%): </label><input type="text" name="txtIGV" id="txtIGV" placeholder="Impuesto al valor agregado (IGV)" value="<?= $igv; ?>" required>
							</div>
							<div class="alertFormEmpresa" style="display: none;"></div>
							<div>
								<button type="submit" class="btn_save btnChangePass"><i class="far fa-save fa-lg"></i> Guardar Datos</button>
							</div>
						</form>
					</div>
				<?php  } ?>
			</div>
		</div>
		</div>
		</div>
		<!-- graficaaa -->
		<br>
		<div>
			<div class="container-grafico" style="display:flex">
				<!-- Formulario 1 -->
				<div style="display:flex; width: 100%;">
					<div style="width:25%;">
						<div style="display: block;text-align: center;width: 350px;">
							<form id="dateForm" style="width:275px">
								<h3 style="text-align:center;">LISTA DE VENTAS</h3>
								<label for="start_date">Fecha de inicio:</label>
								<input type="date" id="start_date" name="start_date">
								<label for="end_date">Fecha de fin:</label>
								<input type="date" id="end_date" name="end_date">
								<input type="button" value="Total de ingreso actual" id="miBoton" style="margin:10px 0;">
								<label for="year">Año:</label>
								<select id="year" name="year"></select>
								<input type="button" value="Total de ingresos anual" id="miBoton3" style="margin:10px 0;">
							</form>
						</div>
					</div>
					<div style="width:75%">
						<div class="grafica" style="width:100%;">
							<div>
								<canvas id="myChart"></canvas>
							</div>
						</div>
						<div id="total_general" style="text-align: center;"></div>
					</div>
				</div>
			</div>
			<br> <br>
			<!-- Formulario 2 -->
			<div style="display:flex">
				<div style="width:100%; display:flex">
					<div style="width:25%;">
						<div style="display: block;text-align: center;width: 350px;">
							<form id="dateForm1" style="width: 275px;">
								<h3 style="text-align:center;">LISTA DE GANANCIAS Y PERDIDAS</h3>
								<label for="year2">Año:</label>
								<select id="year2" name="year2"></select>
								<input type="button" value="Estado Financiero Anual" id="miBoton2" style="margin:10px 0;">
							</form>
						</div>
					</div>
					<div style="width:75%">
						<div class="grafica1" style="width:100%;">
							<div>
								<canvas id="myChart1"></canvas>
							</div>
						</div>
					
						<div id="total_general1" style="text-align: center;"></div>
					</div>
				</div>
			</div>
		</div>

		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script>
			var selectYear = document.getElementById('year');
			var currentYear = new Date().getFullYear();
			var startYear = 2015; // Año inicial
			var endYear = 2035; // Año final

			for (var year = startYear; year <= endYear; year++) {
				var option = document.createElement('option');
				option.value = year;
				option.text = year;
				selectYear.appendChild(option);
			}

			var selectYear2 = document.getElementById('year2');
			var startYear2 = 2015; // Año inicial para el formulario 2
			var endYear2 = 2035; // Año final para el formulario 2

			for (var year2 = startYear2; year2 <= endYear2; year2++) {
				var option2 = document.createElement('option');
				option2.value = year2;
				option2.text = year2;
				selectYear2.appendChild(option2);
			}
			// Declarar las variables para almacenar las instancias de las gráficas
			var myChart;
			var myChart1;

			$(document).ready(function() {
				function generateFixedColor(count) {
					var color = 'rgba(0,0,0)';
					var colors = [];

					for (var i = 0; i < count; i++) {
						colors.push(color);
					}

					return colors;
				}

				// Función para ajustar el tamaño de la gráfica
				function resizeChart() {
					if (myChart) {
						myChart.resize();
					}
				}

				$(window).on('resize', function() {
					// Ajustar el tamaño de la gráfica al cambiar el tamaño de la ventana
					resizeChart();
				});

				// formulario 1 para el mes 
				$(document).on('click', '#miBoton', function(event) {
					event.preventDefault();

					var startDate = $('#start_date').val();
					var endDate = $('#end_date').val();

					$.ajax({
						type: 'POST',
						url: 'ajax1.php',
						data: {
							start_date: startDate,
							end_date: endDate
						},
						success: function(response) {
							var data = JSON.parse(response);
							var labels = data.labels;
							var totals = data.totals;
							var totalGeneral = data.total_general;
							var fixedColor = generateFixedColor(totals.length);
							var barColor = 'rgba(17, 185, 10, 0.9)';

							if (myChart) {
								myChart.data.labels = labels;
								myChart.data.datasets[0].data = totals;
								myChart.data.datasets[0].backgroundColor = fixedColor;
								myChart.data.datasets[0].borderColor = barColor;
								myChart.options.maintainAspectRatio = true;
								myChart.update();
								resizeChart();
							} else {
								var ctx = document.getElementById('myChart').getContext('2d');

								myChart = new Chart(ctx, {
									type: 'line',
									data: {
										labels: labels,
										datasets: [{
											label: 'Cantidad total',
											data: totals,
											backgroundColor: fixedColor,
											borderColor: barColor,
											borderWidth: 3
										}]
									},
									options: {
										plugins: {
											datalabels: {
												anchor: 'end',
												align: 'end',
												color: 'black',
												font: {
													size: 12
												}
											}
										},
										scales: {
											x: {
												grid: {
													color: 'rgba(0, 0, 0, 0.1)'
												}
											},
											y: {
												grid: {
													color: 'rgba(0, 0, 0, 0.1)'
												},
												ticks: {
													beginAtZero: true
												}
											}
										},
										legend: {
											display: false
										},
										maintainAspectRatio: true
									}
								});
							}
							document.getElementById('total_general').textContent = 'Total general: ' + totalGeneral;
						},
						error: function() {
							console.log('Error al cargar los datos de la gráfica.');
						}
					});
				});
				// formulario 1 para año
				$(document).on('click', '#miBoton3', function(event) {
					event.preventDefault();

					var selectedYear = $('#year').val();

					$.ajax({
						type: 'POST',
						url: 'ajax1.php',
						data: {
							year: selectedYear
						},
						success: function(response) {
							var data = JSON.parse(response);
							var labels = data.labels;
							var totals = data.totals;
							var totalGeneral = data.total_general;
							var fixedColor = generateFixedColor(totals.length);
							var barColor = 'rgba(17, 185, 10, 0.9)';

							if (myChart) {
								myChart.data.labels = labels;
								myChart.data.datasets[0].data = totals;
								myChart.data.datasets[0].backgroundColor = fixedColor;
								myChart.data.datasets[0].borderColor = barColor;
								myChart.options.maintainAspectRatio = true;
								myChart.update();
								resizeChart();
							} else {
								var ctx = document.getElementById('myChart').getContext('2d');

								myChart = new Chart(ctx, {
									type: 'line',
									data: {
										labels: labels,
										datasets: [{
											label: 'Cantidad total',
											data: totals,
											backgroundColor: fixedColor,
											borderColor: barColor,
											borderWidth: 3
										}]
									},
									options: {
										plugins: {
											datalabels: {
												anchor: 'end',
												align: 'end',
												color: 'black',
												font: {
													size: 12
												}
											}
										},
										scales: {
											x: {
												grid: {
													color: 'rgba(0, 0, 0, 0.1)'
												}
											},
											y: {
												grid: {
													color: 'rgba(0, 0, 0, 0.1)'
												},
												ticks: {
													beginAtZero: true
												}
											}
										},
										legend: {
											display: false
										},
										maintainAspectRatio: true
									}
								});
							}
							document.getElementById('total_general').textContent = 'Total general: ' + totalGeneral;
						},
						error: function() {
							console.log('Error al cargar los datos de la gráfica.');
						}
					});
				});

				//formulario 2 para el año

				// Evento al hacer clic en el botón "Mostrar gráfica" del formulario 2
				$(document).on('click', '#miBoton2', function(event) {
					event.preventDefault();

					var selectedYear2 = $('#year2').val();

					$.ajax({
						type: 'POST',
						url: 'ajax1.php',
						data: {
							year2: selectedYear2
						},
						success: function(response) {
							var data = JSON.parse(response);
							var labels = data.labels;
							var totals = data.totals;
							var totalResta = data.total_resta;
							
							var fixedColor = generateFixedColor(totals.length);
							var barColor = 'rgba(17, 185, 10, 0.9)';

							if (myChart1) {
								myChart1.data.labels = labels;
								myChart1.data.datasets[0].data = totals;
								myChart1.data.datasets[0].backgroundColor = fixedColor;
								myChart1.data.datasets[0].borderColor = barColor;
								myChart1.options.maintainAspectRatio = true;
								myChart1.update();
								resizeChart();
							} else {
								var ctx = document.getElementById('myChart1').getContext('2d');

								myChart1 = new Chart(ctx, {
									type: 'line',
									data: {
										labels: labels,
										datasets: [{
											label: 'Cantidad total',
											data: totals,
											backgroundColor: fixedColor,
											borderColor: barColor,
											borderWidth: 3
										}]
									},
									options: {
										plugins: {
											datalabels: {
												anchor: 'end',
												align: 'end',
												color: 'black',
												font: {
													size: 12
												}
											}
										},
										scales: {
											x: {
												grid: {
													color: 'rgba(0, 0, 0, 0.1)'
												}
											},
											y: {
												grid: {
													color: 'rgba(0, 0, 0, 0.1)'
												},
												ticks: {
													beginAtZero: true
												}
											}
										},
										legend: {
											display: false
										},
										maintainAspectRatio: true
									}
								});
							}

						
							var totalRestaText = 'Total del año: ' + totalResta;
							document.getElementById('total_general1').textContent = totalRestaText;
							
							resizeChart();
						},
						error: function() {
							console.log('Error al cargar los datos de la gráfica.');
						}
					});
				});

				// Función para cargar los años en el formulario 2
				function loadYears2() {
					var currentYear = new Date().getFullYear();
					var yearSelect2 = $('#year2');

					for (var i = currentYear; i >= 2000; i--) {
						yearSelect2.append($('<option></option>').attr('value', i).text(i));
					}
				}




			});
		</script>
		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>

</html>
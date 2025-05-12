<?php
include('../conexion.php');

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Verificar si los campos de fecha están vacíos
    if (!empty($startDate) && !empty($endDate)) {
        // Consulta SQL con los límites de fecha para el formulario 1
        $stmt = $conexion->prepare("SELECT DATE(com_fechaemi) AS fecha, SUM(com_totalfactura) AS suma_total
        FROM comprobante
        WHERE com_fechaemi BETWEEN ? AND ?
        GROUP BY fecha");

        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        // Array para almacenar las etiquetas de fecha y las sumas totales
        $data = [];
        $labels = [];
        $totals = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        $stmt->close();

        // Ordenar los datos por fecha de manera ascendente
        usort($data, function($a, $b) {
            return strcmp($a['fecha'], $b['fecha']);
        });

        // Obtener las etiquetas de fecha y las sumas totales ordenadas
        foreach ($data as $row) {
            $labels[] = $row['fecha'];
            $totals[] = $row['suma_total'];
        }

        // Obtener el total general para el formulario 1
        $stmtTotal = $conexion->prepare("SELECT SUM(com_totalfactura) AS total_general
        FROM comprobante
        WHERE com_fechaemi BETWEEN ? AND ?");
        $stmtTotal->bind_param("ss", $startDate, $endDate);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        $totalResult = $resultTotal->fetch_assoc();
        $totalGeneral = $totalResult['total_general'];

        // Enviar los datos de respuesta en formato JSON
        echo json_encode(array('labels' => $labels, 'totals' => $totals, 'total_general' => $totalGeneral));
        exit(); // Terminar la ejecución del script después de enviar la respuesta JSON
    } else {
        // Si alguno de los campos está vacío, enviar una respuesta de error
        echo json_encode(array('error' => 'Uno o ambos campos de fecha están vacíos'));
        exit();
    }
}

// Obtener el año seleccionado del formulario 1
if (isset($_POST['year'])) {
    $selectedYear = $_POST['year'];

    // Consulta SQL para obtener los totales por mes del año seleccionado
    $stmt = $conexion->prepare("SELECT MONTH(com_fechaemi) AS mes, SUM(com_totalfactura) AS suma_total
                                FROM comprobante
                                WHERE YEAR(com_fechaemi) = ?
                                GROUP BY mes");
    $stmt->bind_param("s", $selectedYear);
    $stmt->execute();
    $result = $stmt->get_result();

    // Array para almacenar los nombres de los meses y las sumas totales
    $data = [];
    $labels = [];
    $totals = [];

    // Obtener los nombres de los meses en español
    $meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    $stmt->close();

    // Ordenar los datos por mes de manera ascendente
    usort($data, function($a, $b) {
        return $a['mes'] - $b['mes'];
    });

    // Obtener los nombres de los meses y las sumas totales ordenados
    foreach ($data as $row) {
        $mes = intval($row['mes']) - 1; // Restar 1 para coincidir con el índice del array
        $labels[] = $meses[$mes];
        $totals[] = $row['suma_total'];
    }

    // Obtener el total general del año seleccionado
    $stmtTotal = $conexion->prepare("SELECT SUM(com_totalfactura) AS total_general
                                    FROM comprobante
                                    WHERE YEAR(com_fechaemi) = ?");
    $stmtTotal->bind_param("s", $selectedYear);
    $stmtTotal->execute();
    $resultTotal = $stmtTotal->get_result();
    $totalResult = $resultTotal->fetch_assoc();
    $totalGeneral = $totalResult['total_general'];

    // Enviar los datos de respuesta en formato JSON
    echo json_encode(array('labels' => $labels, 'totals' => $totals, 'total_general' => $totalGeneral));
    exit(); // Terminar la ejecución del script después de enviar la respuesta JSON
}



if (isset($_POST['year2'])) {
    $selectedYear2 = $_POST['year2'];

    // Consulta SQL para obtener los totales de ingreso por mes del año seleccionado en el formulario 2
    $stmtIngreso = $conexion->prepare("SELECT MONTH(com_fechaemi) AS mes, SUM(com_totalfactura) AS suma_total
                                    FROM comprobante
                                    WHERE YEAR(com_fechaemi) = ?
                                    GROUP BY mes");

    $stmtIngreso->bind_param("s", $selectedYear2);
    $stmtIngreso->execute();
    $resultIngreso = $stmtIngreso->get_result();

    // Array para almacenar los nombres de los meses y los totales de ingreso
    $data = [];

    $totalIngresos = 0; // Variable para almacenar la suma total de los ingresos
    $totalEgresos = 0; // Variable para almacenar la suma total de los egresos

    $meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    if ($resultIngreso->num_rows > 0) {
        while ($dataIngreso = $resultIngreso->fetch_assoc()) {
            $mesIngreso = intval($dataIngreso['mes']);
            $mesNombre = $meses[$mesIngreso - 1];
            $totalsIngreso = $dataIngreso['suma_total'];

            $totalIngresos += $totalsIngreso; // Sumar el total de ingresos acumulados

            // Consulta SQL para obtener el total de egresos por mes del año seleccionado en el formulario 2
            $stmtEgreso = $conexion->prepare("CALL obtener_totales_egresos(?, ?)");

            $stmtEgreso->bind_param("ss", $selectedYear2, $dataIngreso['mes']);
            $stmtEgreso->execute();
            $resultEgreso = $stmtEgreso->get_result();

            $totalsEgreso = 0;
            if ($resultEgreso->num_rows > 0) {
                while ($dataEgreso = $resultEgreso->fetch_assoc()) {
                    $totalsEgreso += $dataEgreso['suma_total'];
                }
            }

            $resultEgreso->close();
            $stmtEgreso->close();

            $data[] = array(
                'mes' => $mesNombre,
                'total' => $totalsIngreso - $totalsEgreso // Restar los egresos de los ingresos de cada mes
            );

            $totalEgresos += $totalsEgreso; // Sumar los egresos de cada mes al total de egresos acumulados
        }
    }
    $stmtIngreso->close();

    $totalResta = $totalIngresos - $totalEgresos; // Calcular la diferencia entre ingresos y egresos acumulados

    // Ordenar los meses en orden ascendente
    usort($data, function($a, $b) {
        return strcmp($a['mes'], $b['mes']);
    });

    // Invertir el orden de los meses para obtener el orden ascendente
    $data = array_reverse($data);

    // Extraer los datos ordenados en arrays separados
    $labels2 = array_column($data, 'mes');
    $totals2 = array_column($data, 'total');

    // Enviar los datos de respuesta en formato JSON
    echo json_encode(array(
        'labels' => $labels2,
        'totals' => $totals2,
        'total_resta' => $totalResta
    ));
    exit(); // Terminar la ejecución del script después de enviar la respuesta JSON
}

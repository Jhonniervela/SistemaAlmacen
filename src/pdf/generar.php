<?php
require_once 'fpdf/fpdf.php';

// Verificar si se recibió el ID de venta
if (!isset($_GET['v'])) {
    die('Falta el parámetro necesario para generar el PDF.');
}

// Obtener el ID de venta desde la URL
$id_venta = $_GET['v'];

// Construir la URL correctamente con los parámetros GET
$url = "http://localhost/Almacen/ventas?id_venta=" . urlencode($id_venta);

// Realizar la solicitud cURL
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

if ($response === false) {
    die('Error al realizar la solicitud cURL: ' . curl_error($curl));
}

$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

// Verificar el estado HTTP de la respuesta
if ($http_status !== 200) {
    die('Error al obtener los datos de la venta. Estado HTTP: ' . $http_status);
}

// Decodificar la respuesta JSON
$data = json_decode($response, true);

if ($data === null || !isset($data['Detalles']) || !is_array($data['Detalles'])) {
    die('Error al decodificar el JSON o los datos de ventas no son válidos.');
}

// Crear el objeto FPDF y definir la estructura del PDF
$pdf = new FPDF('P', 'mm', 'letter');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetTitle("Detalle de Venta");

// Encabezado del PDF
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Detalle de Venta', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);

// Mostrar los detalles de la venta
$pdf->Ln(10);
$pdf->Cell(40, 10, 'ID Venta:', 0, 0);
$pdf->Cell(0, 10, $id_venta, 0, 1);

if (count($data['Detalles']) > 0) {
    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(10, 10, '#', 1, 0, 'C', true);
    $pdf->Cell(90, 10, 'Cliente', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Precio Unitario', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C', true);

    // Detalles de cada producto
    $pdf->SetFont('Arial', '', 10);
    $total = 0;
    $contador = 1;
    foreach ($data['Detalles'] as $detalle) {
        $pdf->Cell(10, 10, $contador, 1, 0, 'C');
        // Verificar si las claves están definidas antes de usarlas
        $nombrecliente = isset($detalle['nombrecliente']) ? utf8_decode($detalle['nombrecliente']) : '';
        $cantidad = isset($detalle['cantidad']) ? $detalle['cantidad'] : 0;
        $precio = isset($detalle['precio']) ? $detalle['precio'] : 0;
        $pdf->Cell(90, 10, $nombrecliente, 1, 0);
        $pdf->Cell(30, 10, $cantidad, 1, 0, 'C');
        $pdf->Cell(30, 10, '$ ' . number_format($precio, 2), 1, 0, 'R');
        $subtotal = $cantidad * $precio;
        $pdf->Cell(40, 10, '$ ' . number_format($subtotal, 2), 1, 1, 'R');
        $total += $subtotal;
        $contador++;
    }

    // Mostrar el total
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(130, 10, 'Total:', 1, 0, 'R', true);
    $pdf->Cell(40, 10, '$ ' . number_format($total, 2), 1, 1, 'R', true);
} else {
    // No hay detalles disponibles
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'No hay detalles disponibles para esta venta.', 1, 1, 'C');
}

// Salida del PDF al navegador con nombre de archivo "detalle_venta.pdf"
$pdf->Output("detalle_venta.pdf", "I");
?>

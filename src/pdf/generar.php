<?php
require_once 'fpdf/fpdf.php';

// URL de la API de proveedores
$url_proveedores = "http://localhost/Almacen/proveedor";

// Realizar la solicitud cURL para obtener los datos de proveedores
$curl_proveedores = curl_init();

curl_setopt_array($curl_proveedores, array(
  CURLOPT_URL => $url_proveedores,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response_proveedores = curl_exec($curl_proveedores);

if ($response_proveedores === false) {
    die('Error al realizar la solicitud cURL de proveedores: ' . curl_error($curl_proveedores));
}

$http_status_proveedores = curl_getinfo($curl_proveedores, CURLINFO_HTTP_CODE);

curl_close($curl_proveedores);

// Verificar el estado HTTP de la respuesta de proveedores
if ($http_status_proveedores !== 200) {
    die('Error al obtener los datos de proveedores. Estado HTTP: ' . $http_status_proveedores);
}

// Intentar decodificar la respuesta JSON de proveedores
$data_proveedores = json_decode(trim($response_proveedores), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error al decodificar el JSON de proveedores: ' . json_last_error_msg());
}

// Verificar si los datos de proveedores son válidos
if (!isset($data_proveedores['Detalles']) || !is_array($data_proveedores['Detalles'])) {
    die('Los datos de proveedores no son válidos o no están en el formato esperado.');
}

// URL de la API de clientes
$url_clientes = "http://localhost/Almacen/cliente";

// Realizar la solicitud cURL para obtener los datos de clientes
$curl_clientes = curl_init();

curl_setopt_array($curl_clientes, array(
  CURLOPT_URL => $url_clientes,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response_clientes = curl_exec($curl_clientes);

if ($response_clientes === false) {
    die('Error al realizar la solicitud cURL de clientes: ' . curl_error($curl_clientes));
}

$http_status_clientes = curl_getinfo($curl_clientes, CURLINFO_HTTP_CODE);

curl_close($curl_clientes);

// Verificar el estado HTTP de la respuesta de clientes
if ($http_status_clientes !== 200) {
    die('Error al obtener los datos de clientes. Estado HTTP: ' . $http_status_clientes);
}

// Intentar decodificar la respuesta JSON de clientes
$data_clientes = json_decode(trim($response_clientes), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error al decodificar el JSON de clientes: ' . json_last_error_msg());
}

// Verificar si los datos de clientes son válidos
if (!isset($data_clientes['Detalles']) || !is_array($data_clientes['Detalles'])) {
    die('Los datos de clientes no son válidos o no están en el formato esperado.');
}

// Crear el objeto FPDF y definir la estructura del PDF
$pdf = new FPDF('P', 'mm', 'letter');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetTitle("Boleta de Proveedores y Clientes");

// Encabezado del PDF
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Boleta de Proveedores y Clientes', 0, 1, 'C');

// Datos de Proveedores
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Proveedores', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);

foreach ($data_proveedores['Detalles'] as $proveedor) {
    $pdf->Cell(0, 10, utf8_decode('Nombre: ' . $proveedor['nombreproveedor']), 0, 1);
    $pdf->Cell(0, 10, utf8_decode('Contacto: ' . $proveedor['contacto']), 0, 1);
    $pdf->Cell(0, 10, utf8_decode('Dirección: ' . $proveedor['direccion']), 0, 1);
    $pdf->Ln(5); // Espacio entre proveedores
}

// Datos de Clientes
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Clientes', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);

foreach ($data_clientes['Detalles'] as $cliente) {
    $pdf->Cell(0, 10, utf8_decode('Nombre: ' . $cliente['Nombre']), 0, 1);
    $pdf->Ln(5); // Espacio entre clientes
}

// Salida del PDF al navegador con nombre de archivo "boleta_proveedores_clientes.pdf"
$pdf->Output("boleta_proveedores_clientes.pdf", "I");
?>

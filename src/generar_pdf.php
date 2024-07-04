<?php
// Incluir el encabezado y la conexión
include_once "includes/header.php";
require("../conexion.php");

// Obtener el ID de la venta desde la URL
if (isset($_GET['id_venta'])) {
    $id_venta = $_GET['id_venta'];

    // Consultar la información de la venta y detalles desde la base de datos
    $sql_venta = "SELECT * FROM ventas WHERE id_venta = ?";
    $stmt_venta = mysqli_prepare($conexion, $sql_venta);
    mysqli_stmt_bind_param($stmt_venta, "i", $id_venta);
    mysqli_stmt_execute($stmt_venta);
    $result_venta = mysqli_stmt_get_result($stmt_venta);
    $venta = mysqli_fetch_assoc($result_venta);

    if (!$venta) {
        die('Venta no encontrada.');
    }

    $sql_detalles = "SELECT * FROM detalleventa WHERE id_venta = ?";
    $stmt_detalles = mysqli_prepare($conexion, $sql_detalles);
    mysqli_stmt_bind_param($stmt_detalles, "i", $id_venta);
    mysqli_stmt_execute($stmt_detalles);
    $result_detalles = mysqli_stmt_get_result($stmt_detalles);
    $detalles = mysqli_fetch_all($result_detalles, MYSQLI_ASSOC);

    // Generar el contenido del PDF (ejemplo básico)
    require('fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Venta ID: ' . $id_venta);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Cliente: ' . $venta['clienteid']); // Ejemplo, ajustar según tu estructura de datos
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Fecha: ' . $venta['fechaventa']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Detalles de la Venta:');
    $pdf->Ln();

    foreach ($detalles as $detalle) {
        $pdf->Cell(40, 10, 'Producto: ' . $detalle['idproducto'] . ' - Cantidad: ' . $detalle['cantidad']);
        $pdf->Ln();
    }

    $pdf->Output();
} else {
    die('ID de venta no proporcionado.');
}

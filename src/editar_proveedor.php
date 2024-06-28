<?php
include_once "includes/header.php";
// Obtener el ID del proveedor desde los parámetros de la URL
$idproveedor = $_GET['id'];

// Realizar la solicitud cURL para obtener los detalles del proveedor a editar
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost/Almacen/proveedor/' . $idproveedor,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

// Verificar el código de estado HTTP y procesar la respuesta
if ($http_status == 200) {
    // Limpiar la respuesta JSON para eliminar el contenido de DebugBar (si es necesario)
    $response_clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $response);
    $response_clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $response_clean);
    $response_clean = preg_replace('/<[^>]+>/', '', $response_clean);

    // Decodificar el JSON limpio
    $data = json_decode($response_clean, true);

    // Verificar si se obtuvieron los detalles del proveedor correctamente
    if (isset($data['Detalles'])) {
        $proveedor = $data['Detalles']; // Asignar los detalles del proveedor a una variable
    } else {
        die("Error: No se pudieron obtener los detalles del proveedor.");
    }
} else {
    die("Error al obtener los detalles del proveedor. Estado HTTP: " . $http_status);
}

// Procesar el formulario cuando se envía la solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validar y limpiar los datos recibidos del formulario
    $nombreproveedor = htmlspecialchars($_POST['nombreproveedor']);
    $contacto = htmlspecialchars($_POST['contacto']);
    $direccion = htmlspecialchars($_POST['direccion']);

    // Realizar la solicitud cURL para actualizar el proveedor
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/Almacen/proveedor/' . $idproveedor,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT', // Método HTTP para actualizar
        CURLOPT_POSTFIELDS => http_build_query(array(
            'nombreproveedor' => $nombreproveedor,
            'contacto' => $contacto,
            'direccion' => $direccion
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // Verificar el código de estado HTTP de la respuesta de actualización
    if ($http_status == 200) {
        // Mostrar mensaje de éxito
        echo '<div class="alert alert-success" role="alert">Proveedor actualizado correctamente.</div>';
    } else {
        // Mostrar mensaje de error
        echo '<div class="alert alert-danger" role="alert">Error al actualizar el proveedor. Estado HTTP: ' . $http_status . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Proveedor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Editar Proveedor
                </div>
                <div class="card-body">
                    <form action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $idproveedor ?>" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="nombreproveedor">Nombre</label>
                            <input type="text" class="form-control" id="nombreproveedor" name="nombreproveedor" value="<?= htmlspecialchars($proveedor['nombreproveedor']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contacto">Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto" value="<?= htmlspecialchars($proveedor['contacto']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($proveedor['direccion']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Guardar Cambios</button>
                        <a href="proveedor.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
<?php include_once "includes/footer.php"; ?>

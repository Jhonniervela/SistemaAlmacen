<?php
include_once "includes/header.php";

// Function to perform cURL requests
function perform_curl_request($url, $method = 'GET', $postfields = null) {
    $curl = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
    ];

    if ($method == 'POST' && $postfields) {
        $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
        $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/x-www-form-urlencoded'];
    }

    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [$response, $http_status];
}

// Function to sanitize inputs
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Variables para manejar la respuesta de la solicitud de clientes y ventas
$response_clientes = "";
$http_status_clientes = 0;
$response_ventas = "";
$http_status_ventas = 0;

// Verificar si se envió el formulario de nueva venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $fechaventa = sanitize_input($_POST['fechaventa']);
    $clienteid = sanitize_input($_POST['clienteid']);
   
    // Realizar la solicitud cURL para agregar la nueva venta
    list($response, $http_status) = perform_curl_request('http://localhost/Almacen/ventas', 'POST', [
        'fechaventa' => $fechaventa,
        'clienteid' => $clienteid,
    ]);

    if ($http_status == 200) {
        echo "<p class='alert alert-success'>Venta agregada exitosamente.</p>";
    } else {
        echo "<p class='alert alert-danger'>Error al agregar la venta. Código HTTP: {$http_status}</p>";
    }
}

// Realizar la solicitud cURL para obtener la lista de clientes
list($response_clientes, $http_status_clientes) = perform_curl_request('http://localhost/Almacen/cliente');

// Depuración de la respuesta para verificar el contenido recibido
error_log("Response Clientes: " . $response_clientes);

// Decodificar el JSON de clientes
$clientes_data = json_decode($response_clientes, true);

// Verificar el contenido de la variable $clientes_data
error_log("Clientes Data: " . print_r($clientes_data, true));

// Obtener la lista de ventas
list($response_ventas, $http_status_ventas) = perform_curl_request('http://localhost/Almacen/ventas');

// Limpiar la respuesta JSON para eliminar el contenido de DebugBar
$response_clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $response_ventas);
$response_clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $response_clean);
$response_clean = preg_replace('/<[^>]+>/', '', $response_clean);

// Decodificar el JSON limpio de ventas
$data = json_decode($response_clean, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lista de Ventas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <!-- Botón para abrir el modal de nueva venta -->
    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#nueva_venta"><i class="fas fa-plus"></i> Nueva Venta</button>

    <!-- Modal para agregar nueva venta -->
    <div id="nueva_venta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="my-modal-title">Nueva Venta</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="fechaventa">Fecha Venta</label>
                            <input type="date" class="form-control" placeholder="Ingrese Fecha de Venta" name="fechaventa" id="fechaventa" required>
                        </div>
                        <div class="form-group">
                            <label for="clienteid">Cliente</label>
                            <select class="form-control" name="clienteid" id="clienteid" required>
                                <?php if ($http_status_clientes == 200 && !empty($clientes_data) && isset($clientes_data['clientes'])): ?>
                                    <?php foreach ($clientes_data['clientes'] as $cliente): ?>
                                        <option value="<?= htmlspecialchars($cliente['clienteid']) ?>">
                                            <?= htmlspecialchars($cliente['Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No hay clientes disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <input type="submit" value="Registrar" class="btn btn-primary" name="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mostrar la lista de ventas -->
    <?php if ($http_status_ventas == 200 && !empty($data)): ?>
        <?php if (isset($data['Detalles']) && is_array($data['Detalles'])): ?>
            <div class="table-responsive mt-4">
                <table class="table table-hover table-striped table-bordered" id="tbl">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha de Venta</th>
                            <th>Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['Detalles'] as $venta): ?>
                            <tr>
                                <td><?= htmlspecialchars($venta['idventa']) ?></td>
                                <td><?= htmlspecialchars($venta['fechaventa']) ?></td>
                                <td><?= htmlspecialchars($venta['Nombre']) ?></td>
                                <td>
                                    <a href="editar_venta.php?id=<?= $venta['idventa'] ?>" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Editar</a>
                                
                                    <form action="eliminar_venta.php?id=<?= $venta['idventa'] ?>" method="post" class="confirmar d-inline">
                                        <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <p>No hay ventas disponibles.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            <p>Error al obtener los datos de ventas.</p>
            <p>Estado HTTP: <?= htmlspecialchars($http_status_ventas) ?></p>
            <p>Respuesta: <?= htmlspecialchars($response_clean) ?></p>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php include_once "includes/footer.php"; ?>

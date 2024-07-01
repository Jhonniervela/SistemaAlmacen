<?php
include_once "includes/header.php";

$nombre = $apellidoPaterno = $apellidoMaterno = $dni = $telefono = "";

// Verificar si se envió el formulario de búsqueda por DNI
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $dni = htmlspecialchars($_POST['buscarDNI']);

    // Lógica para buscar el cliente por DNI usando cURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $dni,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer tu_token_aqui'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    
    $persona = json_decode($response, true);
    if ($persona && isset($persona['nombres'])) {
        $nombre = htmlspecialchars($persona['nombres']);
        $apellidoPaterno = htmlspecialchars($persona['apellidoPaterno']);
        $apellidoMaterno = htmlspecialchars($persona['apellidoMaterno']);
    } else {
        echo "<script>alert('No se encontró el DNI.');</script>";
    }
}

// Verificar si se envió el formulario de nuevo cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $nombre = htmlspecialchars($_POST['Nombre']);
    $dni = htmlspecialchars($_POST['DNI']);
    $apellidoPaterno = htmlspecialchars($_POST['ApellidoPaterno']);
    $apellidoMaterno = htmlspecialchars($_POST['ApellidoMaterno']);
    $telefono = htmlspecialchars($_POST['Telefono']);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/Almacen/cliente',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query(array(
            'Nombre' => $nombre,
            'DNI' => $dni,
            'ApellidoPaterno' => $apellidoPaterno,
            'ApellidoMaterno' => $apellidoMaterno,
            'Telefono' => $telefono
        )),
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
}

// Solicitud cURL para obtener la lista de clientes
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost/Almacen/cliente',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
));
$response = curl_exec($curl);
$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$response_clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $response);
$response_clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $response_clean);
$response_clean = preg_replace('/<[^>]+>/', '', $response_clean);
$data = json_decode($response_clean, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lista de Clientes</title>
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
    <!-- Botón para abrir el modal de nuevo cliente -->
    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#nuevo_cliente"><i class="fas fa-plus"></i></button>

    <!-- Modal para agregar nuevo cliente -->
    <div id="nuevo_cliente" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="my-modal-title">Nuevo Cliente</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para buscar cliente por DNI -->
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" class="mb-3">
                        <div class="form-group">
                            <label for="buscarDNI">Buscar por DNI</label>
                            <input type="text" class="form-control" name="buscarDNI" id="buscarDNI" placeholder="Ingrese DNI">
                            <input type="submit" value="Buscar" class="btn btn-secondary mt-2" name="buscar">
                        </div>
                    </form>

                    <!-- Formulario para agregar nuevo cliente -->
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="Nombre">Nombre</label>
                            <input type="text" class="form-control" name="Nombre" id="Nombre" value="<?= $nombre ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="DNI">DNI</label>
                            <input type="text" class="form-control" name="DNI" id="DNI" value="<?= $dni ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ApellidoPaterno">Apellido Paterno</label>
                            <input type="text" class="form-control" name="ApellidoPaterno" id="ApellidoPaterno" value="<?= $apellidoPaterno ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ApellidoMaterno">Apellido Materno</label>
                            <input type="text" class="form-control" name="ApellidoMaterno" id="ApellidoMaterno" value="<?= $apellidoMaterno ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Telefono">Teléfono</label>
                            <input type="text" class="form-control" name="Telefono" id="Telefono" value="<?= $telefono ?>" required>
                        </div>
                        <input type="submit" value="Registrar" class="btn btn-primary" name="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mostrar la lista de clientes -->
    <?php if ($http_status == 200 && !empty($data) && isset($data['Detalles']) && is_array($data['Detalles'])): ?>
        <div class="table-responsive mt-4">
            <table class="table table-hover table-striped table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['Detalles'] as $cliente): ?>
                        <tr>
                            <td><?= htmlspecialchars($cliente['clienteid']) ?></td>
                            <td><?= htmlspecialchars($cliente['Nombre']) ?></td>
                            <td><?= htmlspecialchars($cliente['DNI']) ?></td>
                            <td><?= htmlspecialchars($cliente['ApellidoPaterno']) ?></td>
                            <td><?= htmlspecialchars($cliente['ApellidoMaterno']) ?></td>
                            <td><?= htmlspecialchars($cliente['Telefono']) ?></td>
                            <td>
                                <a href="editar_cliente.php?id=<?= $cliente['clienteid'] ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> 
                                </a>
                                <form action="eliminar_cliente.php?id=<?= $cliente['clienteid'] ?>" method="post" class="confirmar d-inline">
                                    <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Error al decodificar el JSON o no hay datos de clientes disponibles.</p>
        <p>Estado HTTP: <?= htmlspecialchars($http_status) ?></p>
        <p>Respuesta: <?= htmlspecialchars($response_clean) ?></p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php include_once "includes/footer.php"; ?>

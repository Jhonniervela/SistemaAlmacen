<?php
include_once "includes/header.php";

// Verificar si se envió el formulario de nuevo proveedor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validar y limpiar los datos recibidos del formulario
    $nombreproveedor = htmlspecialchars($_POST['nombreproveedor']);
    $contacto = htmlspecialchars($_POST['contacto']);
    $direccion = htmlspecialchars($_POST['direccion']);

    // Realizar la solicitud cURL para agregar el nuevo proveedor
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://localhost/Almacen/proveedor',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
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
}

// Realizar la solicitud cURL para obtener la lista de proveedores
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/Almacen/proveedor',
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

// Limpiar la respuesta JSON para eliminar el contenido de DebugBar
$response_clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $response);
$response_clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $response_clean);
$response_clean = preg_replace('/<[^>]+>/', '', $response_clean);

// Decodificar el JSON limpio
$data = json_decode($response_clean, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lista de Proveedores</title>
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
    <!-- Botón para abrir el modal de nuevo proveedor -->
    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#nuevo_proveedor"><i class="fas fa-plus"></i></button>

    <!-- Modal para agregar nuevo proveedor -->
    <div id="nuevo_proveedor" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="my-modal-title">Nuevo Proveedor</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="nombreproveedor">Nombre</label>
                            <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombreproveedor" id="nombreproveedor" required>
                        </div>
                        <div class="form-group">
                            <label for="contacto">Contacto</label>
                            <input type="text" class="form-control" placeholder="Ingrese Contacto" name="contacto" id="contacto" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" placeholder="Ingrese Dirección" name="direccion" id="direccion" required>
                        </div>
                        <input type="submit" value="Registrar" class="btn btn-primary" name="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mostrar la lista de proveedores -->
    <?php if ($http_status == 200 && !empty($data) && isset($data['Detalles']) && is_array($data['Detalles'])): ?>
        <div class="table-responsive mt-4">
            <table class="table table-hover table-striped table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['Detalles'] as $proveedor): ?>
                        <tr>
                            <td><?= htmlspecialchars($proveedor['idproveedor']) ?></td>
                            <td><?= htmlspecialchars($proveedor['nombreproveedor']) ?></td>
                            <td><?= htmlspecialchars($proveedor['contacto']) ?></td>
                            <td><?= htmlspecialchars($proveedor['direccion']) ?></td>
                            <td>
                                <a href="editar_proveedor.php?id=<?= $proveedor['idproveedor'] ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> 
                                </a>
                                <form action="eliminar_proveedor.php?id=<?= $proveedor['idproveedor'] ?>" method="post" class="confirmar d-inline">
                                    <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Error al decodificar el JSON o no hay datos de proveedores disponibles.</p>
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

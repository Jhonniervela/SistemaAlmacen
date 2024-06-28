<?php
include_once "includes/header.php";

// Verificar si se envió el formulario de nueva categoría
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validar y limpiar los datos recibidos del formulario
    $nombrecategoria = htmlspecialchars($_POST['nombrecategoria']);

    // Realizar la solicitud cURL para agregar la nueva categoría
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://localhost/Almacen/categorias',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => http_build_query(array('nombrecategoria' => $nombrecategoria)),
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

 
}

// Realizar la solicitud cURL para obtener la lista de categorías
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/Almacen/categorias',
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

// Eliminar cualquier otro contenido no JSON
$response_clean = trim(explode('void 0===', $response_clean)[0]);

// Decodificar el JSON limpio
$data = json_decode($response_clean, true);

?>

<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#nuevo_usuario"><i class="fas fa-plus"></i></button>

<div id="nuevo_usuario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">Nueva Categoría</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="nombrecategoria">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombrecategoria" id="nombrecategoria" required>
                    </div>
                    <input type="submit" value="Registrar" class="btn btn-primary" name="submit">
                </form>
            </div>
        </div>
    </div>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lista de Categorías</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Agregar Font Awesome si lo estás usando -->
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

<?php if ($http_status == 200 && !is_null($data) && isset($data['Detalles']) && is_array($data['Detalles'])): ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered mt-2" id="tbl">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['Detalles'] as $categoria): ?>
                    <tr>
                        <td><?= htmlspecialchars($categoria['idcategoria']) ?></td>
                        <td><?= htmlspecialchars($categoria['nombrecategoria']) ?></td>
                        <td>
                            <a href="editar_categoria.php?id=<?= $categoria['idcategoria'] ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i></a>
                            <form action="eliminar_categoria.php?id=<?= $categoria['idcategoria'] ?>" method="post" class="confirmar d-inline">
                                <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Error al decodificar el JSON o no hay datos de categorías disponibles.</p>
    <p>Estado HTTP: <?= htmlspecialchars($http_status) ?></p>
    <p>Respuesta: <?= htmlspecialchars($response_clean) ?></p>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php include_once "includes/footer.php"; ?>

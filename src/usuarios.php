<?php
include_once "includes/header.php";

// Verificar si se envió el formulario de nuevo usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validar y limpiar los datos recibidos del formulario
    $nombreusuario = htmlspecialchars($_POST['nombreusuario']);
    $contraseña = htmlspecialchars($_POST['contraseña']);
    $correo = htmlspecialchars($_POST['correo']);
    $dnitrabajador = htmlspecialchars($_POST['dnitrabajador']);
    $idrol = htmlspecialchars($_POST['idrol']);

    // Realizar la solicitud cURL para agregar el nuevo usuario
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://localhost/Almacen/usuarios',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => http_build_query(array(
          'nombreusuario' => $nombreusuario,
          'contraseña' => $contraseña,
          'correo' => $correo,
          'dnitrabajador' => $dnitrabajador,
          'idrol' => $idrol
      )),
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);

    curl_close($curl);

   
}

// Realizar la solicitud cURL para obtener la lista de usuarios
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/Almacen/usuarios',
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
$curl_error = curl_error($curl);

curl_close($curl);

// Decodificar el JSON
$data = json_decode($response, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
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
    <!-- Botón para abrir el modal de nuevo usuario -->
    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#nuevo_usuario"><i class="fas fa-plus"></i></button>

    <!-- Modal para agregar nuevo usuario -->
    <div id="nuevo_usuario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="my-modal-title">Nuevo Usuario</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="nombreusuario">Nombre</label>
                            <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombreusuario" id="nombreusuario" required>
                        </div>
                        <div class="form-group">
                            <label for="contraseña">Contraseña</label>
                            <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="contraseña" id="contraseña" required>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="text" class="form-control" placeholder="Ingrese Correo" name="correo" id="correo" required>
                        </div>
                        <div class="form-group">
                            <label for="dnitrabajador">DNI Trabajador</label>
                            <input type="text" class="form-control" placeholder="Ingrese DNI" name="dnitrabajador" id="dnitrabajador" required>
                        </div>
                        <div class="form-group">
                            <label for="idrol">ID Rol</label>
                            <input type="text" class="form-control" placeholder="Ingrese ID Rol" name="idrol" id="idrol" required>
                        </div>
                        <input type="submit" value="Registrar" class="btn btn-primary" name="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mostrar la lista de usuarios -->
    <?php if ($http_status == 200 && !empty($data) && is_array($data) && isset($data['Detalles']) && is_array($data['Detalles'])): ?>
        <div class="table-responsive mt-4">
            <table class="table table-hover table-striped table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Contraseña</th>
                        <th>DNI Trabajador</th>
                        <th>Correo</th>
                        <th>ID Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['Detalles'] as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['idusuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombreusuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['contraseña']) ?></td>
                            <td><?= htmlspecialchars($usuario['dnitrabajador']) ?></td>
                            <td><?= htmlspecialchars($usuario['correo']) ?></td>
                            <td><?= htmlspecialchars($usuario['idrol']) ?></td>
                            <td>
                                <a href="editar_usuario.php?id=<?= $usuario['idusuario'] ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> 
                                </a>
                                <form action="eliminar_usuario.php?id=<?= $usuario['idusuario'] ?>" method="post" class="confirmar d-inline">
                                    <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Error al decodificar el JSON o no hay datos de usuarios disponibles.</p>
        <p>Estado HTTP: <?= htmlspecialchars($http_status) ?></p>
        <p>Respuesta: <?= htmlspecialchars($response) ?></p>
        <p>Error cURL: <?= htmlspecialchars($curl_error) ?></p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php include_once "includes/footer.php"; ?>

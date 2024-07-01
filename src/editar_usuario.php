<?php
include_once "includes/header.php";
// Obtener el ID del usuario desde los parámetros de la URL
$idusuario = $_GET['id'];

// Realizar la solicitud cURL para obtener los detalles del usuario a editar
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost/Almacen/usuarios/' . $idusuario,
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

    // Verificar si se obtuvieron los detalles del usuario correctamente
    if (isset($data['Detalles'])) {
        $usuario = $data['Detalles']; // Asignar los detalles del usuario a una variable
    } else {
        die("Error: No se pudieron obtener los detalles del usuario.");
    }
} else {
    die("Error al obtener los detalles del usuario. Estado HTTP: " . $http_status);
}

// Procesar el formulario cuando se envía la solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validar y limpiar los datos recibidos del formulario
    $nombreusuario = htmlspecialchars($_POST['nombreusuario']);
    $contraseña = htmlspecialchars($_POST['contraseña']);
    $correo = htmlspecialchars($_POST['correo']);
    $dnitrabajador = htmlspecialchars($_POST['dnitrabajador']);
    $idrol = htmlspecialchars($_POST['idrol']);

    // Realizar la solicitud cURL para actualizar el usuario
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/Almacen/usuarios/' . $idusuario,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT', // Método HTTP para actualizar
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

    curl_close($curl);

    // Verificar el código de estado HTTP de la respuesta de actualización
    if ($http_status == 200) {
        // Mostrar mensaje de éxito
        echo '<div class="alert alert-success" role="alert">Usuario actualizado correctamente.</div>';
    } else {
        // Mostrar mensaje de error
        echo '<div class="alert alert-danger" role="alert">Error al actualizar el usuario. Estado HTTP: ' . $http_status . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Editar Usuario
                </div>
                <div class="card-body">
                    <form action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $idusuario ?>" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="nombreusuario">Nombre</label>
                            <input type="text" class="form-control" id="nombreusuario" name="nombreusuario" value="<?= htmlspecialchars($usuario['nombreusuario']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contraseña">Contraseña</label>
                            <input type="text" class="form-control" id="contraseña" name="contraseña" value="<?= htmlspecialchars($usuario['contraseña']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="text" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="dnitrabajador">DNI Trabajador</label>
                            <input type="text" class="form-control" id="dnitrabajador" name="dnitrabajador" value="<?= htmlspecialchars($usuario['dnitrabajador']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="idrol">ID Rol</label>
                            <input type="text" class="form-control" id="idrol" name="idrol" value="<?= htmlspecialchars($usuario['idrol']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Guardar Cambios</button>
                        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
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

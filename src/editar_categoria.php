<?php
ob_start(); // Iniciar el búfer de salida

include_once "includes/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Manejo de la solicitud PUT para actualizar la categoría
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/Almacen/categorias/' . $_POST['id'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => http_build_query(array('nombrecategoria' => $_POST["nombrecategoria"])),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    // Redireccionar a la página de categorías después de actualizar
    header("Location: Categoria.php");
    exit();
} else {
    // Obtener detalles de la categoría para mostrar en el formulario de edición
    if (isset($_GET['id'])) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost/Almacen/categorias/' . $_GET['id'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);

        // Verificar si se recibieron los detalles de la categoría correctamente
        if ($data && isset($data['Detalles'])) {
            $categoria = $data['Detalles'];
        } else {
            // Manejar el caso donde no se obtuvieron datos válidos
            echo "Error: No se encontraron detalles de la categoría.";
            exit();
        }
    } else {
        // Manejar el caso donde no se recibió el parámetro id en la URL
        echo "Error: No se recibió el parámetro ID.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
        }
        .card-body {
            padding: 30px;
        }
        .form-label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 20px;
        }
        .btn-primary {
            border-radius: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Editar Categoría
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?= $_SESSION['success_message'] ?>
                        </div>
                        <?php // Limpiar el mensaje de éxito después de mostrarlo
                        unset($_SESSION['success_message']);
                        ?>
                    <?php endif; ?>
                    <form method="post">
                        <div class="form-group row">
                            <label for="nombrecategoria" class="col-sm-3 col-form-label form-label">Nombre:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nombrecategoria" name="nombrecategoria" value="<?= htmlspecialchars($categoria['nombrecategoria']) ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($categoria['idcategoria']) ?>">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Guardar</button>
                                <a href="Categoria.php" class="btn btn-danger">Atrás</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php include_once "includes/footer.php"; ?>
<?php ob_end_flush(); // Limpiar el búfer y enviar la salida al navegador ?>

<?php
ob_start(); // Iniciar el buffer de salida

include_once "includes/header.php";

// Función para obtener categorías desde la API
function obtenerCategoriasDesdeAPI() {
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

    if ($response === false) {
        echo "Error al obtener categorías: " . curl_error($curl);
        return [];
    }

    curl_close($curl);

    // Decodificar la respuesta JSON
    $categorias = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error al decodificar JSON de categorías: " . json_last_error_msg();
        return [];
    }

    // Verificar si la respuesta contiene las categorías esperadas
    if (isset($categorias['Detalles']) && is_array($categorias['Detalles'])) {
        return $categorias['Detalles'];
    } else {
        echo "No se encontraron categorías válidas en la respuesta.";
        var_dump($categorias); // Imprime la respuesta para depuración
        return [];
    }
}

// Función para obtener proveedores desde la API
function obtenerProveedoresDesdeAPI() {
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

    if ($response === false) {
        echo "Error al obtener proveedores: " . curl_error($curl);
        return [];
    }

    curl_close($curl);

    // Decodificar la respuesta JSON
    $proveedores = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error al decodificar JSON de proveedores: " . json_last_error_msg();
        return [];
    }

    // Verificar si la respuesta contiene los proveedores esperados
    if (isset($proveedores['Detalles']) && is_array($proveedores['Detalles'])) {
        return $proveedores['Detalles'];
    } else {
        echo "No se encontraron proveedores válidos en la respuesta.";
        var_dump($proveedores); // Imprime la respuesta para depuración
        return [];
    }
}

// Variables para almacenar los valores del formulario
$nombreproducto = $precioventa = $ubicacionproducto = $codigobarras = $idcategoria = $idproveedor = '';

// Verificar si se envió el formulario de nuevo producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validar y limpiar los datos recibidos del formulario
    $nombreproducto = htmlspecialchars($_POST['nombreproducto']);
    $precioventa = htmlspecialchars($_POST['precioventa']);
    $ubicacionproducto = htmlspecialchars($_POST['ubicacionproducto']);
    $codigobarras = htmlspecialchars($_POST['codigobarras']);
    $idcategoria = htmlspecialchars($_POST['idcategoria']);
    $idproveedor = htmlspecialchars($_POST['idproveedor']);

    // Realizar la solicitud cURL para agregar el nuevo producto
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://localhost/Almacen/productos', // Reemplaza con la URL de tu API
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => http_build_query(array(
          'nombreproducto' => $nombreproducto,
          'precioventa' => $precioventa,
          'ubicacionproducto' => $ubicacionproducto,
          'codigobarras' => $codigobarras,
          'idcategoria' => $idcategoria,
          'idproveedor' => $idproveedor,
      )),
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        echo "Error al agregar producto: " . curl_error($curl);
    }

    curl_close($curl);

    // Redireccionar a la página actual para actualizar la lista de productos
    header("Location: productos.php");
    exit();
}

// Obtener categorías y proveedores
$categorias = obtenerCategoriasDesdeAPI();
$proveedores = obtenerProveedoresDesdeAPI();

?>

<?php include_once "includes/header.php"; ?>

<div class="container mt-4">
    <!-- Formulario para agregar nuevo producto -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            Nuevo Producto
        </div>
        <div class="card-body">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" autocomplete="off">
                <div class="form-group">
                    <label for="nombreproducto">Nombre Producto</label>
                    <input type="text" class="form-control" placeholder="Ingrese el nombre del producto" name="nombreproducto" id="nombreproducto" value="<?= $nombreproducto ?>" required>
                </div>
                <div class="form-group">
                    <label for="precioventa">Precio Venta</label>
                    <input type="text" class="form-control" placeholder="Ingrese Precio de Venta" name="precioventa" id="precioventa" value="<?= $precioventa ?>" required>
                </div>
                <div class="form-group">
                    <label for="ubicacionproducto">Ubicación Producto</label>
                    <input type="text" class="form-control" placeholder="Ingrese Ubicación del Producto" name="ubicacionproducto" id="ubicacionproducto" value="<?= $ubicacionproducto ?>" required>
                </div>
                <div class="form-group">
                    <label for="codigobarras">Código de Barras</label>
                    <input type="text" class="form-control" placeholder="Ingrese Código de Barras" name="codigobarras" id="codigobarras" value="<?= $codigobarras ?>" required>
                </div>
                <div class="form-group">
                    <label for="idcategoria">Categoría</label>
                    <select class="form-control" name="idcategoria" id="idcategoria" required>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= htmlspecialchars($categoria['idcategoria']) ?>"><?= htmlspecialchars($categoria['nombrecategoria']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="idproveedor">Proveedor</label>
                    <select class="form-control" name="idproveedor" id="idproveedor" required>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= htmlspecialchars($proveedor['idproveedor']) ?>"><?= htmlspecialchars($proveedor['nombreproveedor']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="submit" value="Registrar" class="btn btn-primary" name="submit">
                <a href="productos.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

<?php
ob_end_flush(); // Finalizar y enviar el buffer de salida
?>

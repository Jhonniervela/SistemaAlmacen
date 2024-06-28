<?php
include_once "includes/header.php";

// Variables para almacenar los valores del formulario
$nombreproducto = '';
$precioventa = '';
$ubicacionproducto = '';
$codigobarras = '';
$idcategoria = '';
$idproveedor = '';

// Verificar si se recibió el ID del producto a editar
if (isset($_GET['id'])) {
    $idproducto = htmlspecialchars($_GET['id']);

    // Realizar la solicitud cURL para obtener los detalles del producto específico
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/Almacen/productos/' . $idproducto,
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

    if ($response === false) {
        echo "Error en la solicitud cURL: " . curl_error($curl);
    }

    curl_close($curl);

    // Verificar el estado HTTP de la respuesta
    if ($http_status == 200) {
        // Decodificar la respuesta JSON
        $producto = json_decode($response, true);

        // Verificar si se pudo decodificar el JSON y si se encontró el producto
        if (isset($producto['Detalles']) && !empty($producto['Detalles'])) {
            $producto = $producto['Detalles'];

            // Asignar los valores del producto a las variables
            $nombreproducto = isset($producto['nombreproducto']) ? htmlspecialchars($producto['nombreproducto']) : '';
            $precioventa = isset($producto['precioventa']) ? htmlspecialchars($producto['precioventa']) : '';
            $ubicacionproducto = isset($producto['ubicacionproducto']) ? htmlspecialchars($producto['ubicacionproducto']) : '';
            $codigobarras = isset($producto['codigobarras']) ? htmlspecialchars($producto['codigobarras']) : '';
            $idcategoria = isset($producto['idcategoria']) ? htmlspecialchars($producto['idcategoria']) : '';
            $idproveedor = isset($producto['idproveedor']) ? htmlspecialchars($producto['idproveedor']) : '';
        } else {
            echo "<p>Error: Producto no encontrado.</p>";
            exit();
        }
    } else {
        echo "<p>Error al obtener los detalles del producto. Código HTTP: {$http_status}</p>";
        exit();
    }
} else {
    echo "<p>No se ha especificado un ID de producto válido.</p>";
    exit();
}

// Función para obtener todas las categorías desde tu API
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
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // Decodificar la respuesta JSON
    $categorias = json_decode($response, true);

    // Verificar si la solicitud fue exitosa y devolver las categorías
    if ($http_status == 200 && isset($categorias['Detalles']) && is_array($categorias['Detalles'])) {
        return $categorias['Detalles'];
    } else {
        return array(); // Devolver un array vacío si hay un error o no hay categorías
    }
}

// Función para obtener todos los proveedores desde tu API
function obtenerProveedoresDesdeAPI() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/Almacen/proveedores',
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

    // Decodificar la respuesta JSON
    $proveedores = json_decode($response, true);

    // Verificar si la solicitud fue exitosa y devolver los proveedores
    if ($http_status == 200 && isset($proveedores['Detalles']) && is_array($proveedores['Detalles'])) {
        return $proveedores['Detalles'];
    } else {
        return array(); // Devolver un array vacío si hay un error o no hay proveedores
    }
}

?>

<?php include_once "includes/header.php"; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="modal-title">Editar Producto</h5>
        </div>
        <div class="card-body">
            <form action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $idproducto ?>" method="post" autocomplete="off">
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
                        <?php
                        $categorias = obtenerCategoriasDesdeAPI();
                        foreach ($categorias as $categoria):
                            $selected = ($categoria['idcategoria'] == $idcategoria) ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($categoria['idcategoria']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($categoria['nombrecategoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="idproveedor">Proveedor</label>
                    <select class="form-control" name="idproveedor" id="idproveedor" required>
                        <?php
                        $proveedores = obtenerProveedoresDesdeAPI();
                        foreach ($proveedores as $proveedor):
                            $selected = ($proveedor['idproveedor'] == $idproveedor) ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($proveedor['idproveedor']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($proveedor['nombreproveedor']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Agregamos un campo oculto para enviar el método PUT -->
                <input type="hidden" name="_method" value="PUT">
                <input type="submit" value="Actualizar" class="btn btn-primary" name="submit">
            </form>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

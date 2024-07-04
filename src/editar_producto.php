<?php
include_once "includes/header.php";

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
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        echo "Error en la solicitud cURL: " . curl_error($curl);
        curl_close($curl);
        exit();
    }

    // Verificar el estado HTTP de la respuesta
    if ($http_status == 200) {
        // Decodificar la respuesta JSON
        $json_response = json_decode($response, true);

        // Verificar si se pudo decodificar el JSON y si se encontró el producto
        if (isset($json_response['detalles']) && !empty($json_response['detalles'])) {
            $producto = $json_response['detalles'][0]; // Acceder al primer producto del array

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
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        echo "Error en la solicitud cURL: " . curl_error($curl);
        curl_close($curl);
        return array();
    }

    curl_close($curl);

    // Decodificar la respuesta JSON
    $categorias = json_decode($response, true);

    // Verificar si la solicitud fue exitosa y devolver las categorías
    if ($http_status == 200 && isset($categorias['Detalles']) && is_array($categorias['Detalles'])) {
        return $categorias['Detalles'];
    } else {
        echo "<p>Error al obtener categorías desde la API. Código HTTP: {$http_status}</p>";
        return array(); // Devolver un array vacío si hay un error o no hay categorías
    }
}

// Función para obtener todos los proveedores desde tu API
function obtenerProveedoresDesdeAPI() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/Almacen/proveedor',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        echo "Error en la solicitud cURL: " . curl_error($curl);
        curl_close($curl);
        return array();
    }

    curl_close($curl);

    // Decodificar la respuesta JSON
    $proveedores = json_decode($response, true);

    // Verificar si la solicitud fue exitosa y devolver los proveedores
    if ($http_status == 200 && isset($proveedores['Detalles']) && is_array($proveedores['Detalles'])) {
        return $proveedores['Detalles'];
    } else {
        echo "<p>Error al obtener proveedores desde la API. Código HTTP: {$http_status}</p>";
        return array(); // Devolver un array vacío si hay un error o no hay proveedores
    }
}

// Procesar el formulario de actualización cuando se envía
if (isset($_POST['submit'])) {
    // Obtener los datos actualizados del formulario
    $nombreproducto = htmlspecialchars($_POST['nombreproducto']);
    $precioventa = htmlspecialchars($_POST['precioventa']);
    $ubicacionproducto = htmlspecialchars($_POST['ubicacionproducto']);
    $codigobarras = htmlspecialchars($_POST['codigobarras']);
    $idcategoria = htmlspecialchars($_POST['idcategoria']);
    $idproveedor = htmlspecialchars($_POST['idproveedor']);

    // Validar que los campos no estén vacíos
    if (empty($nombreproducto) || empty($precioventa) || empty($ubicacionproducto) || empty($codigobarras) || empty($idcategoria) || empty($idproveedor)) {
        echo "<p class='alert alert-danger'>Por favor, completa todos los campos.</p>";
    } else {
        // Preparar los datos para la solicitud PUT a tu API (ejemplo)
        $datos_producto = array(
            'nombreproducto' => $nombreproducto,
            'precioventa' => $precioventa,
            'ubicacionproducto' => $ubicacionproducto,
            'codigobarras' => $codigobarras,
            'idcategoria' => $idcategoria,
            'idproveedor' => $idproveedor
        );

        // Convertir los datos a formato JSON
        $datos_json = json_encode($datos_producto);

        // Inicializar cURL para enviar la solicitud PUT a la API
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost/Almacen/productos/' . $idproducto,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $datos_json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($datos_json)
            ),
        ));

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            echo "Error en la solicitud cURL: " . curl_error($curl);
            curl_close($curl);
            exit();
        }

        curl_close($curl);

        // Verificar el estado HTTP de la respuesta
        if ($http_status == 200) {
            echo "<p class='alert alert-success'>¡El producto se actualizó correctamente!</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al actualizar el producto. Código HTTP: {$http_status}</p>";
        }
    }
}
?>

<div class="container">
    <h2>Editar Producto</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="nombreproducto">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombreproducto" name="nombreproducto" value="<?php echo $nombreproducto; ?>" required>
        </div>
        <div class="form-group">
            <label for="precioventa">Precio de Venta</label>
            <input type="number" step="0.01" class="form-control" id="precioventa" name="precioventa" value="<?php echo $precioventa; ?>" required>
        </div>
        <div class="form-group">
            <label for="ubicacionproducto">Ubicación del Producto</label>
            <input type="text" class="form-control" id="ubicacionproducto" name="ubicacionproducto" value="<?php echo $ubicacionproducto; ?>" required>
        </div>
        <div class="form-group">
            <label for="codigobarras">Código de Barras</label>
            <input type="text" class="form-control" id="codigobarras" name="codigobarras" value="<?php echo $codigobarras; ?>" required>
        </div>
        <div class="form-group">
            <label for="idcategoria">Categoría</label>
            <select class="form-control" id="idcategoria" name="idcategoria" required>
                <?php
                $categorias = obtenerCategoriasDesdeAPI();
                foreach ($categorias as $categoria) {
                    $selected = ($idcategoria == $categoria['id']) ? 'selected' : '';
                    echo "<option value='{$categoria['id']}' {$selected}>{$categoria['nombrecategoria']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="idproveedor">Proveedor</label>
            <select class="form-control" id="idproveedor" name="idproveedor" required>
                <?php
                $proveedores = obtenerProveedoresDesdeAPI();
                foreach ($proveedores as $proveedor) {
                    $selected = ($idproveedor == $proveedor['id']) ? 'selected' : '';
                    echo "<option value='{$proveedor['id']}' {$selected}>{$proveedor['nombreproveedor']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Actualizar Producto</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>

<?php
include_once "includes/header.php";

// Realizar la solicitud cURL para obtener la lista de productos
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/Almacen/productos',
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
<a href="registrar_producto.php" class="btn btn-primary"><i class="fas fa-plus"></i></a>

<?php include_once "includes/header.php"; ?>

<div class="container mt-4">
    <!-- Table to display products -->
    <?php if ($http_status == 200 && !is_null($data) && isset($data['Detalles']) && is_array($data['Detalles'])): ?>
        <div class="table-responsive mt-4">
            <table class="table table-hover table-striped table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre Producto</th>
                        <th>Precio Venta</th>
                        <th>Ubicación</th>
                        <th>Código de Barras</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['Detalles'] as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['idproducto']) ?></td>
                            <td><?= htmlspecialchars($producto['nombreproducto']) ?></td>
                            <td><?= htmlspecialchars($producto['precioventa']) ?></td>
                            <td><?= htmlspecialchars($producto['ubicacionproducto']) ?></td>
                            <td><?= htmlspecialchars($producto['codigobarras']) ?></td>
                            <td><?= htmlspecialchars($producto['nombrecategoria']) ?></td>
                            <td><?= htmlspecialchars($producto['idproveedor']) ?></td>
                            <td>
                                <a href="editar_producto.php?id=<?= $producto['idproducto'] ?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                <form action="eliminar_producto.php?id=<?= $producto['idproducto'] ?>" method="post" class="d-inline confirmar">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Error al decodificar el JSON o no hay datos de productos disponibles.</p>
        <p>Estado HTTP: <?= htmlspecialchars($http_status) ?></p>
        <p>Respuesta: <?= htmlspecialchars($response_clean) ?></p>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>

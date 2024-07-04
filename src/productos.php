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

// Decodificar la respuesta JSON
$data = json_decode($response, true);

?>
<a href="registrar_producto.php" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Producto</a>

<?php include_once "includes/header.php"; ?>

<div class="container mt-4">
    <!-- Table to display products -->
    <?php if ($http_status == 200 && !empty($data['detalles'])): ?>
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
                    <?php foreach ($data['detalles'] as $producto): ?>
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
        <div class="alert alert-warning mt-4" role="alert">
            No se encontraron productos disponibles.
        </div>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>

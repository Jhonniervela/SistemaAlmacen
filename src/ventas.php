<?php
// Incluir el encabezado y la conexión
include_once "includes/header.php";
require("../conexion.php");

// Verificar si se recibieron datos por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decodificar el JSON enviado desde el frontend
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['clienteid']) && isset($data['productos'])) {
        $clienteid = $data['clienteid'];
        $productos = $data['productos'];

        // Iniciar una transacción
        mysqli_begin_transaction($conexion);

        try {
            // Insertar la venta en la tabla 'ventas'
            $sql_venta = "INSERT INTO ventas (clienteid, fechaventa) VALUES (?, NOW())";
            $stmt_venta = mysqli_prepare($conexion, $sql_venta);
            mysqli_stmt_bind_param($stmt_venta, "i", $clienteid);
            mysqli_stmt_execute($stmt_venta);

            $id_venta = mysqli_insert_id($conexion); // Obtener el ID de la venta insertada

            if (!$id_venta) {
                throw new Exception("Error al insertar la venta");
            }

            // Insertar los detalles de los productos vendidos en la tabla 'detalleventa'
            $sql_detalle_venta = "INSERT INTO detalleventa (idproducto, idventa, cantidad, preciototal) VALUES (?, ?, ?, ?)";
            $stmt_detalle_venta = mysqli_prepare($conexion, $sql_detalle_venta);

            foreach ($productos as $producto) {
                $id_producto = $producto['id'];
                $cantidad = $producto['cantidad'];
                $precio_total = $producto['cantidad'] * $producto['precio'];

                mysqli_stmt_bind_param($stmt_detalle_venta, "iiid", $id_producto, $id_venta, $cantidad, $precio_total);
                mysqli_stmt_execute($stmt_detalle_venta);

                if (mysqli_stmt_affected_rows($stmt_detalle_venta) <= 0) {
                    throw new Exception("Error al insertar detalle de venta");
                }
            }

            // Confirmar la transacción
            mysqli_commit($conexion);

            // Enviar respuesta JSON de éxito
            echo json_encode(['success' => true, 'id_venta' => $id_venta]);
            exit;

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            mysqli_rollback($conexion);
            http_response_code(500); // Establecer código de estado HTTP apropiado para error interno
            echo json_encode(['success' => false, 'error' => 'Error interno al procesar la venta: ' . $e->getMessage()]);
            exit;
        }
    }
}

// Obtener lista de clientes desde la base de datos
$sql_clientes = "SELECT clienteid, nombre FROM cliente";
$resultado_clientes = mysqli_query($conexion, $sql_clientes);

$clientes = array();
if ($resultado_clientes && mysqli_num_rows($resultado_clientes) > 0) {
    $clientes = mysqli_fetch_all($resultado_clientes, MYSQLI_ASSOC);
}

// Obtener lista de productos desde la base de datos
$sql_productos = "SELECT idproducto, nombreproducto, precioventa FROM producto";
$resultado_productos = mysqli_query($conexion, $sql_productos);

$productos = array();
if ($resultado_productos && mysqli_num_rows($resultado_productos) > 0) {
    $productos = mysqli_fetch_all($resultado_productos, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    Datos Venta
                </div>
                <div class="card-body">
                    <!-- Formulario para ingresar datos del cliente y seleccionar cliente existente -->
                    <form id="formCliente" method="POST" action="">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="clienteid">Seleccionar Cliente</label>
                                <select class="form-control" id="clienteid" name="clienteid" required>
                                    <option value="">Seleccionar Cliente</option>
                                    <?php foreach ($clientes as $cliente) : ?>
                                        <option value="<?php echo $cliente['clienteid']; ?>"><?php echo $cliente['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tabla para mostrar productos y permitir selección -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Producto</th>
                                        <th>Precio de Venta</th>
                                        <th>Fecha de Venta</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_productos">
                                    <?php foreach ($productos as $producto) : ?>
                                        <tr id="producto_<?php echo $producto['idproducto']; ?>">
                                            <td><?php echo $producto['idproducto']; ?></td>
                                            <td><?php echo $producto['nombreproducto']; ?></td>
                                            <td><?php echo $producto['precioventa']; ?></td>
                                            <td><input type="date" id="fechaventa_<?php echo $producto['idproducto']; ?>" class="form-control" value="<?php echo date('Y-m-d'); ?>"></td>
                                            <td>
                                                <button type="button" class="btn btn-success" onclick="agregarProducto(<?php echo $producto['idproducto']; ?>, '<?php echo $producto['nombreproducto']; ?>', <?php echo $producto['precioventa']; ?>)">Agregar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- Tabla para mostrar productos seleccionados -->
                        <h4>Productos Seleccionados:</h4>
                        <div id="productos_seleccionados"></div>

                        <a href="pdf/generar.php?cl=<?= $venta['clienteid'] ?>&v=<?= $venta['idventa'] ?>" target="_blank" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let productosSeleccionados = [];

    // Función para agregar productos seleccionados
    function agregarProducto(id, nombre, precio) {
        const fechaventa = document.getElementById('fechaventa_' + id).value;
        const producto = { id, nombre, precio, fechaventa, cantidad: 1, total: precio };
        productosSeleccionados.push(producto);
        mostrarProductosSeleccionados();
    }

    // Función para mostrar los productos seleccionados
    function mostrarProductosSeleccionados() {
        const divProductosSeleccionados = document.getElementById('productos_seleccionados');
        divProductosSeleccionados.innerHTML = '';

        if (productosSeleccionados.length === 0) {
            divProductosSeleccionados.innerHTML = '<p>No se han seleccionado productos.</p>';
            return;
        }

        const tabla = document.createElement('table');
        tabla.className = 'table table-bordered';
        tabla.innerHTML = `
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Producto</th>
                    <th>Precio Unitario</th>
                    <th>Fecha de Venta</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
        `;

        productosSeleccionados.forEach(producto => {
            const total = producto.cantidad * producto.precio;
            tabla.innerHTML += `
                <tr>
                    <td>${producto.id}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.precio}</td>
                    <td>${producto.fechaventa}</td>
                    <td>${producto.cantidad}</td>
                    <td>${total}</td>
                </tr>
            `;
        });

        tabla.innerHTML += '</tbody>';
        divProductosSeleccionados.appendChild(tabla);
    }

    // Función para generar la venta
    function generarVenta() {
        const clienteid = document.getElementById('clienteid').value.trim();

        if (clienteid === '') {
            alert('Por favor, selecciona un cliente.');
            return;
        }

        if (productosSeleccionados.length === 0) {
            alert('No has seleccionado ningún producto.');
            return;
        }

        const data = {
            clienteid: clienteid,
            productos: productosSeleccionados
        };

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'ventas.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function () {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    alert('Venta registrada correctamente.');
                    productosSeleccionados = []; // Limpiar productos seleccionados después de registrar la venta
                    mostrarProductosSeleccionados(); // Actualizar la vista de productos seleccionados
                    location.reload(); // Recargar la página después de la venta
                } else {
                    alert('Error al registrar la venta: ' + response.error);
                }
            }
        };
        xhr.send(JSON.stringify(data));
    }
</script>

</body>
</html>

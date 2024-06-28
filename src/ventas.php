<?php
include_once "includes/header.php";
require("../conexion.php");

$id_user = $_SESSION['idUser'];

// Función para manejar solicitudes AJAX
if (isset($_POST['ajax']) && $_POST['ajax'] == 1 && isset($_POST['nombre_producto'])) {
    $nombre_producto = $_POST['nombre_producto'];
    $sql_productos = "SELECT * FROM producto WHERE nombreproducto LIKE '%$nombre_producto%'";
    $resultado = mysqli_query($conexion, $sql_productos);

    $productos = array();
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $productos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }

    echo json_encode($productos);
    exit; // Terminar la ejecución después de manejar la solicitud AJAX
}

// Mostrar todos los productos cuando se carga la página por primera vez
$sql_todos_productos = "SELECT * FROM producto";
$resultado_todos = mysqli_query($conexion, $sql_todos_productos);

if ($resultado_todos && mysqli_num_rows($resultado_todos) > 0) {
    $productos = mysqli_fetch_all($resultado_todos, MYSQLI_ASSOC);
} else {
    $productos = array(); // Si no hay productos en la base de datos, inicializar como array vacío
}
?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h4 class="text-center">Datos del Cliente</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <div class="row">
                        <div class="col-lg-4">
                            <div>
                                <input type="hidden" id="idcliente" value="1" name="idcliente" required>
                                <label>Nombre</label>
                                <input type="text" name="nom_cliente" id="nom_cliente" class="form-control" placeholder="Ingrese nombre del cliente" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="number" name="tel_cliente" id="tel_cliente" class="form-control" disabled required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" name="dir_cliente" id="dir_cliente" class="form-control" disabled required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                Datos Venta
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> VENDEDOR</label>
                            <p style="font-size: 16px; text-transform: uppercase; color: red;"><?php echo $_SESSION['nombre']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                Buscar Producto por Nombre
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="buscar_producto" class="form-control" type="text" name="buscar_producto" placeholder="Ingresa el nombre del producto">
                                    <button type="button" class="btn btn-primary mt-2" onclick="buscarProducto()">Buscar</button>
                                    <div id="resultados_busqueda" class="list-group mt-2"></div>
                                    <small id="mensaje_busqueda" class="form-text text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="tblDetalle">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Nombre del Producto</th>
                        <th>Precio de Venta</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="productosBusqueda">
                    <!-- Aquí se agregarán los productos encontrados en la búsqueda -->
                    <?php foreach ($productos as $producto) : ?>
                        <tr id="producto_<?php echo $producto['idproducto']; ?>">
                            <td><?php echo $producto['idproducto']; ?></td>
                            <td><?php echo $producto['nombreproducto']; ?></td>
                            <td><?php echo $producto['precioventa']; ?></td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="agregarProducto(<?php echo $producto['idproducto']; ?>, '<?php echo $producto['nombreproducto']; ?>', <?php echo $producto['precioventa']; ?>)">Agregar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-hover" id="tblVenta">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Nombre del Producto</th>
                        <th>Precio de Venta</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="productosVenta">
                    <!-- Aquí se agregarán los productos seleccionados para la venta -->
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <a href="#" class="btn btn-primary" id="btn_generar"><i class="fas fa-save"></i> Generar Venta</a>
        </div>
    </div>
    <script>
    let productosSeleccionados = [];

    function buscarProducto() {
        const nombreProducto = document.getElementById('buscar_producto').value.trim();

        if (nombreProducto.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'ventas.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    const productos = JSON.parse(this.responseText);
                    mostrarResultadosBusqueda(productos);
                }
            };
            xhr.send('ajax=1&nombre_producto=' + encodeURIComponent(nombreProducto));
        } else {
            mostrarTodosLosProductos();
        }
    }

    function mostrarResultadosBusqueda(productos) {
        const resultadosBusqueda = document.getElementById('productosBusqueda');
        resultadosBusqueda.innerHTML = '';

        if (productos.length > 0) {
            productos.forEach(producto => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${producto.idproducto}</td>
                    <td>${producto.nombreproducto}</td>
                    <td>${producto.precioventa}</td>
                    <td>
                        <button class="btn btn-success btn-sm" onclick="agregarProducto(${producto.idproducto}, '${producto.nombreproducto}', ${producto.precioventa})">Agregar</button>
                    </td>
                `;
                resultadosBusqueda.appendChild(tr);
            });
            document.getElementById('mensaje_busqueda').textContent = ''; // Limpiar mensaje de error si lo hubiera
        } else {
            document.getElementById('mensaje_busqueda').textContent = 'No se encontraron productos.';
        }
    }

    function mostrarTodosLosProductos() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'ventas.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                const productos = JSON.parse(this.responseText);
                const resultadosBusqueda = document.getElementById('productosBusqueda');
                resultadosBusqueda.innerHTML = '';
                productos.forEach(producto => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${producto.idproducto}</td>
                        <td>${producto.nombreproducto}</td>
                        <td>${producto.precioventa}</td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="agregarProducto(${producto.idproducto}, '${producto.nombreproducto}', ${producto.precioventa})">Agregar</button>
                        </td>
                    `;
                    resultadosBusqueda.appendChild(tr);
                });
                document.getElementById('mensaje_busqueda').textContent = ''; // Limpiar mensaje de error si lo hubiera
            }
        };
        xhr.send('ajax=1'); // Enviar solicitud AJAX para obtener todos los productos
    }

    function agregarProducto(id, nombre, precio) {
        const producto = { id, nombre, precio, cantidad: 1, total: precio };
        productosSeleccionados.push(producto);
        actualizarTablaVenta();
    }

    function actualizarTablaVenta() {
        const tbody = document.getElementById('productosVenta');
        tbody.innerHTML = '';
        productosSeleccionados.forEach((producto, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${producto.id}</td>
                <td>${producto.nombre}</td>
                <td>${producto.precio}</td>
                <td>
                    <input type="number" value="${producto.cantidad}" min="1" onchange="cambiarCantidad(${index}, this.value)" class="form-control">
                </td>
                <td>${producto.total}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function cambiarCantidad(index, cantidad) {
        productosSeleccionados[index].cantidad = cantidad;
        productosSeleccionados[index].total = productosSeleccionados[index].precio * cantidad;
        actualizarTablaVenta();
    }

    function eliminarProducto(index) {
        productosSeleccionados.splice(index, 1);
        actualizarTablaVenta();
    }
</script>

</div>

<?php include_once "includes/footer.php"; ?>

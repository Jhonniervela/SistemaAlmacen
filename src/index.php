<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['active']) || $_SESSION['active'] !== true) {
    header('Location: index.php');
    exit;
}

// Incluir encabezado y conexión a la base de datos
include_once "includes/header.php";
require "../conexion.php";

// Consultas para obtener los totales
$usuarios = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM usuario");
$totalU = mysqli_fetch_assoc($usuarios)['total'];

$proveedores = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM proveedor");
$totalC = mysqli_fetch_assoc($proveedores)['total'];

$productos = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM producto");
$totalP = mysqli_fetch_assoc($productos)['total'];

$ventas = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM venta");
$totalV = mysqli_fetch_assoc($ventas)['total'];

// Obtener lista de productos mediante cURL
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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray">Panel de Administración</h1>
</div>

<!-- Content Row -->
<div class="row">
    <a class="col-xl-3 col-md-6 mb-4" href="usuarios.php">
        <div class="card border-left-primary shadow h-100 py-2 bg-warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Usuarios</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?= $totalU ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <a class="col-xl-3 col-md-6 mb-4" href="proveedor.php">
        <div class="card border-left-success shadow h-100 py-2 bg-success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Proveedores</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?= $totalC ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <a class="col-xl-3 col-md-6 mb-4" href="productos.php">
        <div class="card border-left-info shadow h-100 py-2 bg-primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Productos</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-white"><?= $totalP ?></div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <a class="col-xl-3 col-md-6 mb-4" href="lista_ventas.php">
        <div class="card border-left-warning bg-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Ventas</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?= $totalV ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-white-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="container mt-4">
    <!-- Table to display products -->
    <div class="row">
        <div class="col-md-6 text-left">
            <?php if ($http_status == 200 && !empty($data['detalles'])): ?>
                <div class="table-responsive mt-4">
                    <table class="table table-hover table-striped table-bordered" id="tbl">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Producto</th>
                                <th>Precio Venta</th>
                                <th>Código de Barras</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['detalles'] as $producto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($producto['idproducto']) ?></td>
                                    <td><?= htmlspecialchars($producto['nombreproducto']) ?></td>
                                    <td><?= htmlspecialchars($producto['precioventa']) ?></td>
                                    <td><?= htmlspecialchars($producto['codigobarras']) ?></td>
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
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

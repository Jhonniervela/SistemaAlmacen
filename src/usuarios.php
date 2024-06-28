<?php
include_once "includes/header.php";
require "../conexion.php";

$id_user = $_SESSION['idUser'];
$alert = "";

if (!empty($_POST)) {
    if (empty($_POST['idusuario']) || empty($_POST['nombreusuario']) || empty($_POST['correo']) || empty($_POST['dnitrabajador']) || empty($_POST['idrol'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son requeridos</div>';
    } else {
        $idusuario = $_POST['idusuario'];
        $nombreusuario = $_POST['nombreusuario'];
        $correo = $_POST['correo'];
        $dnitrabajador = $_POST['dnitrabajador'];
        $idrol = $_POST['idrol'];
        
        // Verificar si el correo ya existe en la base de datos
        $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE correo = '$correo'");
        if (mysqli_num_rows($query) > 0) {
            $alert = '<div class="alert alert-warning" role="alert">El correo ya existe</div>';
        } else {
            // Insertar nuevo usuario en la base de datos
            $query_insert = mysqli_query($conexion, "INSERT INTO usuario(idusuario, nombreusuario, correo, dnitrabajador, idrol) VALUES ('$idusuario', '$nombreusuario', '$correo', '$dnitrabajador', '$idrol')");
            if ($query_insert) {
                $alert = '<div class="alert alert-primary" role="alert">Usuario registrado exitosamente</div>';
                header("Location: usuarios.php");
                exit; // Detener la ejecución del script después de redirigir
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al registrar usuario</div>';
            }
        }
    }
}
?>

<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#nuevo_usuario"><i class="fas fa-plus"></i></button>
<div id="nuevo_usuario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="form-group">
                        <label for="idusuario">ID</label>
                        <input type="text" class="form-control" placeholder="Ingrese ID" name="idusuario" id="idusuario">
                    </div>
                    <div class="form-group">
                        <label for="nombreusuario">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombreusuario" id="nombreusuario">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" placeholder="Ingrese Correo Electrónico" name="correo" id="correo">
                    </div>
                    <div class="form-group">
                        <label for="dnitrabajador">DNI Trabajador</label>
                        <input type="text" class="form-control" placeholder="Ingrese DNI del Trabajador" name="dnitrabajador" id="dnitrabajador">
                    </div>
                    <div class="form-group">
                        <label for="idrol">Rol</label>
                        <select class="form-control" name="idrol" id="idrol">
                            <?php
                            $query_roles = mysqli_query($conexion, "SELECT * FROM rol");
                            while ($rol = mysqli_fetch_assoc($query_roles)) {
                                echo "<option value='{$rol['idrol']}'>{$rol['nombrerol']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <input type="submit" value="Registrar" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered mt-2" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>DNI Trabajador</th>
                <th>ID Rol</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($conexion, "SELECT * FROM usuario");
            $result = mysqli_num_rows($query);
            if ($result > 0) {
                while ($data = mysqli_fetch_assoc($query)) {
            ?>
                    <tr>
                        <td><?php echo $data['idusuario']; ?></td>
                        <td><?php echo $data['nombreusuario']; ?></td>
                        <td><?php echo $data['correo']; ?></td>
                        <td><?php echo $data['dnitrabajador']; ?></td>
                        <td><?php echo $data['idrol']; ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $data['idusuario']; ?>" class="btn btn-success"><i class='fas fa-edit'></i></a>
                            <form action="eliminar_usuario.php?id=<?php echo $data['idusuario']; ?>" method="post" class="confirmar d-inline">
                                <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                            </form>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</div>
<?php include_once "includes/footer.php";

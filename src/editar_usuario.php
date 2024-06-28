<?php
include_once "includes/header.php";
require "../conexion.php";

$id_user = $_SESSION['idUser'];
$alert = "";

if (!empty($_POST)) {
    if (empty($_POST['nombreusuario']) || empty($_POST['correo']) || empty($_POST['dnitrabajador']) || empty($_POST['idrol'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son requeridos</div>';
    } else {
        $idusuario = $_POST['id'];
        $nombreusuario = $_POST['nombreusuario'];
        $correo = $_POST['correo'];
        $dnitrabajador = $_POST['dnitrabajador'];
        $idrol = $_POST['idrol'];
        
        // Evitar inyección SQL usando consultas preparadas
        $sql_update = "UPDATE usuario SET nombreusuario = ?, correo = ?, dnitrabajador = ?, idrol = ? WHERE idusuario = ?";
        $stmt = mysqli_prepare($conexion, $sql_update);
        mysqli_stmt_bind_param($stmt, "ssiii", $nombreusuario, $correo, $dnitrabajador, $idrol, $idusuario);
        
        if (mysqli_stmt_execute($stmt)) {
            $alert = '<div class="alert alert-success" role="alert">Usuario actualizado</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al actualizar usuario</div>';
        }
    }
}

// Mostrar Datos
if (empty($_REQUEST['id'])) {
    header("Location: usuarios.php");
    exit; // Detener la ejecución después de redirigir
}

$idusuario = $_REQUEST['id'];
$sql_select = "SELECT * FROM usuario WHERE idusuario = ?";
$stmt = mysqli_prepare($conexion, $sql_select);
mysqli_stmt_bind_param($stmt, "i", $idusuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: usuarios.php");
    exit; // Detener la ejecución después de redirigir
} else {
    $data = mysqli_fetch_array($result);
    $idcliente = $data['idusuario'];
    $nombreusuario = $data['nombreusuario'];
    $correo = $data['correo'];
    $dnitrabajador = $data['dnitrabajador'];
    $idrol = $data['idrol'];
}
?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Modificar Usuario
            </div>
            <div class="card-body">
                <form class="" action="" method="post">
                    <?php echo $alert; ?>
                    <input type="hidden" name="id" value="<?php echo $idusuario; ?>">
                    <div class="form-group">
                        <label for="nombreusuario">Nombre</label>
                        <input type="text" placeholder="Ingrese nombre" class="form-control" name="nombreusuario" id="nombreusuario" value="<?php echo $nombreusuario; ?>">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="text" placeholder="Ingrese correo" class="form-control" name="correo" id="correo" value="<?php echo $correo; ?>">
                    </div>
                    <div class="form-group">
                        <label for="dnitrabajador">DNI Trabajador</label>
                        <input type="text" placeholder="Ingrese DNI Trabajador" class="form-control" name="dnitrabajador" id="dnitrabajador" value="<?php echo $dnitrabajador; ?>">
                    </div>
                    <div class="form-group">
                        <label for="idrol">Rol</label>
                        <select class="form-control" name="idrol" id="idrol">
                            <?php
                            // Consulta para obtener los roles disponibles
                            $sql_roles = "SELECT idrol, nombrerol FROM rol";
                            $result_roles = mysqli_query($conexion, $sql_roles);

                            // Iterar sobre los resultados y generar las opciones del select
                            while ($row = mysqli_fetch_assoc($result_roles)) {
                                $idrol_opt = $row['idrol'];
                                $nombrerol = $row['nombrerol'];
                                // Comprueba si el rol actual es el seleccionado y agrégale el atributo "selected" si es así
                                $selected = ($idrol_opt == $idrol) ? "selected" : "";
                                echo "<option value='$idrol_opt' $selected>$nombrerol</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Actualizar</button>
                    <a href="usuarios.php" class="btn btn-danger">Atrás</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

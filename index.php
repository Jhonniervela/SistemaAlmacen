<?php
session_start();

// Variable para almacenar mensajes de alerta
$alert = '';

// Procesar el inicio de sesión si se envió el formulario
if (!empty($_POST)) {
    // Si se envió el formulario de inicio de sesión
    if (isset($_POST['login'])) {
        if (empty($_POST['usuario']) || empty($_POST['clave'])) {
            $alert = '<div class="alert alert-danger" role="alert">
            Ingrese su usuario y su clave
            </div>';
        } else {
            require_once "conexion.php";
            
            $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
            $clave = $_POST['clave'];
            
            // Consulta preparada para evitar inyección SQL
            $query = mysqli_prepare($conexion, "SELECT idusuario, nombreusuario, correo, contraseña FROM usuario WHERE nombreusuario = ?");
            
            mysqli_stmt_bind_param($query, "s", $user);
            
            mysqli_stmt_execute($query);
            
            mysqli_stmt_store_result($query);
            
            if (mysqli_stmt_num_rows($query) > 0) {
                mysqli_stmt_bind_result($query, $idusuario, $nombreusuario, $correo, $stored_password);
                
                mysqli_stmt_fetch($query);
                
                // Verificar la contraseña ingresada con la almacenada
                if ($clave === $stored_password) {
                    $_SESSION['active'] = true;
                    $_SESSION['idUser'] = $idusuario;
                    $_SESSION['nombre'] = $nombreusuario;
                    $_SESSION['user'] = $user;
                    
                    header('location: src/');
                    exit; // Detener la ejecución después de redireccionar
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">
                    Usuario o Contraseña Incorrecta
                    </div>';
                }
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                Usuario o Contraseña Incorrecta
                </div>';
            }
            
            mysqli_stmt_close($query);
            mysqli_close($conexion);
        }
    }

    // Si se envió el formulario de registro
    if (isset($_POST['registro'])) {
        require_once "conexion.php";
        
        // Recibir y sanitizar los datos del formulario
        $nombreUsuario = mysqli_real_escape_string($conexion, $_POST['nombreUsuario']);
        $email = mysqli_real_escape_string($conexion, $_POST['email']);
        $password = $_POST['password'];
        $dnitrabajador = mysqli_real_escape_string($conexion, $_POST['dnitrabajador']);
        $idrol = !empty($_POST['idrol']) ? mysqli_real_escape_string($conexion, $_POST['idrol']) : null;
    
        // Consulta preparada para insertar el nuevo usuario
        $query = mysqli_prepare($conexion, "INSERT INTO usuario (nombreusuario, correo, contraseña, dnitrabajador, idrol) VALUES (?, ?, ?, ?, ?)");
        
        mysqli_stmt_bind_param($query, "ssssi", $nombreUsuario, $email, $password, $dnitrabajador, $idrol);
    
        if (mysqli_stmt_execute($query)) {
            // Registro exitoso, enviar mensaje de confirmación o redireccionar a donde desees
            $alert = '<div class="alert alert-success" role="alert">
            ¡Registro exitoso! Puedes iniciar sesión ahora.
            </div>';
        } else {
            // Error al ejecutar la consulta
            $alert = '<div class="alert alert-danger" role="alert">
            Error al registrar el usuario: ' . mysqli_error($conexion) . '
            </div>';
        }
    
        mysqli_stmt_close($query);
        mysqli_close($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Iniciar Sesión</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <script src="assets/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header text-center">
                                    <img class="img-thumbnail" src="assets/img/images.png" width="100">
                                    <h3 class="font-weight-light my-4">Iniciar Sesión</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Formulario de Inicio de Sesión -->
                                    <form action="" method="POST">
                                        <div class="form-group">
                                            <label class="small mb-1" for="usuario"><i class="fas fa-user"></i> Usuario</label>
                                            <input class="form-control py-4" id="usuario" name="usuario" type="text" placeholder="Ingrese usuario" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="clave"><i class="fas fa-key"></i> Contraseña</label>
                                            <input class="form-control py-4" id="clave" name="clave" type="password" placeholder="Ingrese Contraseña" required />
                                        </div>
                                        <div class="alert alert-danger text-center <?php echo empty($alert) ? 'd-none' : ''; ?>" id="alerta" role="alert">
                                            <?php echo $alert; ?>
                                        </div>
                                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" type="submit" name="login">Ingresar</button>
                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#registroModal">Registrarse</button>
                                        </div>
                                    </form>
                                    <!-- Fin Formulario de Inicio de Sesión -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; <a href="http://angelsifuentes.com/" target="_blank" rel="noopener noreferrer">Visite mi página web</a> <?php echo date("Y"); ?></div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal de Registro -->
    <div class="modal fade" id="registroModal" tabindex="-1" role="dialog" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Registro de Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulario de Registro -->
                    <form action="" method="POST">
                        <input type="hidden" name="registro" value="true">
                        <div class="form-group">
                            <label for="nombreUsuario">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" placeholder="Ingrese nombre de usuario" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese correo electrónico" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese contraseña" required>
                        </div>
                        <div class="form-group">
                            <label for="dnitrabajador">DNI Trabajador</label>
                            <input type="text" class="form-control" id="dnitrabajador" name="dnitrabajador" placeholder="Ingrese DNI del trabajador" required>
                        </div>
                        <div class="form-group">
                            <label for="idrol">ID Rol (opcional)</label>
                            <input type="text" class="form-control" id="idrol" name="idrol" placeholder="Ingrese ID del rol">
                        </div>
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </form>
                    <!-- Fin Formulario de Registro -->
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>

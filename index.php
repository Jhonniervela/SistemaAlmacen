<?php
session_start();
if (!empty($_SESSION['active'])) {
    header('location: src/');
    exit; // Detener la ejecución después de redireccionar
}

if (!empty($_POST)) {
    $alert = '';
    if (empty($_POST['usuario']) || empty($_POST['clave'])) {
        $alert = '<div class="alert alert-danger" role="alert">
        Ingrese su usuario y su clave
        </div>';
    } else {
        require_once "conexion.php";
        
        $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
        $clave = $_POST['clave'];
        
        // Consulta preparada para evitar inyección SQL
        $query = mysqli_prepare($conexion, "SELECT idusuario, nombreusuario, correo FROM usuario WHERE nombreusuario = ? AND contraseña = ?");
        
        mysqli_stmt_bind_param($query, "ss", $user, $clave);
        
        mysqli_stmt_execute($query);
        
        mysqli_stmt_store_result($query);
        
        if (mysqli_stmt_num_rows($query) > 0) {
            mysqli_stmt_bind_result($query, $idusuario, $nombreusuario, $correo);
            
            mysqli_stmt_fetch($query);
            
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
        
        mysqli_stmt_close($query);
        mysqli_close($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Iniciar Sesión</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
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
                                            <button class="btn btn-primary" type="submit">Ingresar</button>
                                        </div>
                                    </form>
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
    <script src="assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>

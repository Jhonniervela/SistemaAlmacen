<?php
require("../conexion.php");

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $query_delete = mysqli_query($conexion, "DELETE FROM usuario WHERE idusuario = $id");
    if ($query_delete) {
        header("Location: usuarios.php");
        exit(); // Detener la ejecución después de redirigir
    } else {
        echo "Error al intentar eliminar el usuario.";
    }
} else {
    echo "No se proporcionó un ID de usuario válido.";
}
?>

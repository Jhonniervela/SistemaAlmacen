<?php
$host = 'localhost';  // Nombre del host
$db = 'almacen';  // Nombre de la base de datos
$user = 'root';  // Nombre de usuario
$password = '';  // Contraseña

// Crear la conexión
$conexion = new mysqli($host, $user, $password, $db);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
} else {
    echo "";
}
?>

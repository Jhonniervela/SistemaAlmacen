<?php
$curl = curl_init();

$id = isset($_GET['id']) ? $_GET['id'] : ''; // Verificar si se proporciona el ID en la URL

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/Almacen/categorias/' . $id, // Añadir "/" antes de concatenar el ID
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'DELETE',
));

$response = curl_exec($curl);

curl_close($curl);

?>

<?php
$token = 'apis-token-4752.FuPQzXf8Q4hhMP6jzl-x49JiCOw9akI-';

if (isset($_POST['dni'])) {
    $dni = $_POST['dni'];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $dni,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Referer: https://apis.net.pe/consulta-dni-api',
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($http_status == 403) {
        $responseData = array(
            'success' => false,
            'message' => 'Error 403: Acceso prohibido. Verifica el token de autorización.'
        );
    } elseif ($http_status >= 400) {
        $responseData = array(
            'success' => false,
            'message' => 'Error al consultar el DNI. Código de error HTTP: ' . $http_status
        );
    } else {
        $persona = json_decode($response);

        if ($persona && isset($persona->nombres)) {
            $responseData = array(
                'success' => true,
                'data' => array(
                    'nombres' => $persona->nombres,
                    'apellidoPaterno' => $persona->apellidoPaterno,
                    'apellidoMaterno' => $persona->apellidoMaterno
                )
            );
        } else {
            $responseData = array(
                'success' => false,
                'message' => 'No se encontraron datos para el DNI proporcionado.'
            );
        }
    }

    curl_close($curl);
} else {
    $responseData = array(
        'success' => false,
        'message' => 'No se recibió el valor del DNI.'
    );
}

// Enviar respuesta como JSON
header('Content-Type: application/json');
echo json_encode($responseData);
?>

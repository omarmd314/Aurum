<?php

class Github extends Controller {

    public function webhook (){
    
    $secret = '#Realmadrid7';

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Asegúrate de que la solicitud provenga de GitHub.
        //$signature = ;
        if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE_256'])) {
            http_response_code(403);
            die('Forbidden: Missing signature');
        }
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];
    
        // Recibe el payload del webhook.
        $payload = file_get_contents('php://input');
    
        // Calcula la firma del payload para compararla con la que envía GitHub.
        $calculated_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
        // Compara las firmas. Si no coinciden, la solicitud no es válida.
        if (!hash_equals($signature, $calculated_signature)) {
            http_response_code(403);
            die('Forbidden: Invalid signature');
        }
    
        // Decodifica el JSON del payload.
        $data = json_decode($payload, true);
    
        // Ahora puedes procesar el evento. Este ejemplo se enfoca en un evento 'push'.
        $event = $_SERVER['HTTP_X_GITHUB_EVENT'];
    
        if ($event === 'push') {
            // Si la solicitud es un 'push', ejecuta un 'git pull'.
            // Asegúrate de que el usuario de Apache (www-data) tenga permisos para ejecutar 'git'.
            // Puedes usar la función `exec` o `shell_exec`.
            // Es vital que sepas lo que haces y tomes las precauciones de seguridad adecuadas.
            exec('git pull origin main');
            header('Content-Type: application/json');
            echo json_encode(["Mensaje"=> "¡Actualización de código exitosa!" ]);

        } else {

            header('Content-Type: application/json');
            echo json_encode(["Mensaje"=> "Evento recibido: ".$event]);
        }
    
        // Envía una respuesta HTTP 200 OK para confirmar a GitHub que la solicitud se recibió correctamente.
        http_response_code(200);
        } else {
            header('Content-Type: application/json');
            echo json_encode(["Mensaje"=> "Solo se aceptan peticiones post" ]);
        }
    } 

}
<?php
/**
 * API Endpoint: Formulario de Contacto
 * Guarda mensajes en Supabase contact_messages
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del request (soportar tanto JSON como form-data)
$input_data = [];
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($content_type, 'application/json') !== false) {
    $json_input = file_get_contents('php://input');
    $input_data = json_decode($json_input, true) ?? [];
} else {
    $input_data = $_POST;
}

// Validar campos requeridos
$name = sanitize_input($input_data['name'] ?? '');
$email = sanitize_input($input_data['email'] ?? '');
$phone = sanitize_input($input_data['phone'] ?? '');
$subject = sanitize_input($input_data['subject'] ?? '');
$message = sanitize_input($input_data['message'] ?? '');
$property_id = sanitize_input($input_data['property_id'] ?? '');
$property_title = sanitize_input($input_data['property_title'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos']);
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

// Rate limiting: verificar que no haya más de 3 mensajes en la última hora desde la misma IP
$user_ip = $_SERVER['REMOTE_ADDR'];
$one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));

// Obtener mensajes recientes de esta IP
$recent_messages = supabase_get('contact_messages', [
    'ip_address' => 'eq.' . $user_ip,
    'created_at' => 'gte.' . $one_hour_ago
]);

if ($recent_messages && count($recent_messages) >= 3) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Has enviado demasiados mensajes. Por favor intenta más tarde.'
    ]);
    exit;
}

// SI hay property_id, armar el mensaje con datos VERIFICADOS del servidor
if (!empty($property_id)) {
    $prop_data = supabase_get('properties', ['id' => 'eq.' . $property_id], 'id,title,type,price,location,features');
    if ($prop_data && count($prop_data) > 0) {
        $p = $prop_data[0];

        // Determinar moneda
        $currency = 'DOP';
        $features = !empty($p['features']) ? (is_array($p['features']) ? $p['features'] : pg_array_to_php_array($p['features'])) : [];
        if (in_array('USD', $features))
            $currency = 'USD';

        $price_formatted = format_price($p['price'], $currency);

        $verified_summary = "\n\n--- DETALLES DE LA PROPIEDAD ---\n";
        $verified_summary .= "Propiedad: " . $p['title'] . "\n";
        $verified_summary .= "ID: " . $p['id'] . "\n";
        $verified_summary .= "Tipo: " . $p['type'] . "\n";
        $verified_summary .= "Precio: " . $price_formatted . "\n";
        $verified_summary .= "Ubicación: " . $p['location'] . "\n";
        $verified_summary .= "------------------------------";

        $message .= $verified_summary;
    }
}

// Preparar datos para insertar
$data = [
    'name' => $name,
    'email' => $email,
    'phone' => !empty($phone) ? $phone : null,
    'property_id' => !empty($property_id) ? $property_id : null,
    'message' => $message,
    'ip_address' => $user_ip,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
    'status' => 'new'
];

// Insertar en Supabase
$result = supabase_insert('contact_messages', $data);

if ($result) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al enviar el mensaje. Por favor intenta de nuevo.'
    ]);
}

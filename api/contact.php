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

// Verificar CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit;
}

// Validar campos requeridos
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');

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

// Preparar datos para insertar
$data = [
    'name' => $name,
    'email' => $email,
    'phone' => !empty($phone) ? $phone : null,
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

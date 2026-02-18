<?php
/**
 * API - Eliminar Propiedad
 * Ibron Inmobiliaria
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';

// Verificar sesión de administrador
if (!is_authenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

// Obtener datos de la petición (POST)
$data = json_decode(file_get_contents('php://input'), true);
$property_id = $data['id'] ?? null;

if (!$property_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de propiedad requerido']);
    exit;
}

// 1. Obtener datos de la propiedad para borrar imágenes del storage
$property = supabase_get('properties', ['id' => 'eq.' . $property_id], '*');

if ($property && count($property) > 0) {
    $p = $property[0];
    
    // Lista de imágenes a borrar
    $images_to_delete = [];
    
    // Imagen principal
    if (!empty($p['image_main'])) {
        $images_to_delete[] = str_replace(SUPABASE_URL . '/storage/v1/object/public/property-images/', '', $p['image_main']);
    }
    
    // Galería
    if (!empty($p['image_gallery'])) {
        $gallery = pg_array_to_php_array($p['image_gallery']);
        foreach ($gallery as $img) {
            $images_to_delete[] = str_replace(SUPABASE_URL . '/storage/v1/object/public/property-images/', '', $img);
        }
    }
    
    // Borrar imágenes de Supabase Storage
    foreach ($images_to_delete as $path) {
        $clean_path = ltrim($path, '/');
        $success = supabase_storage_delete('property-images', $clean_path);
        if ($success) {
            log_error("Deleted storage file: $clean_path");
        } else {
            log_error("Failed to delete storage file: $clean_path");
        }
    }
}

// 2. Eliminar registro de la base de datos
$result = supabase_delete('properties', $property_id);

if ($result !== false) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al eliminar la propiedad de la base de datos']);
}

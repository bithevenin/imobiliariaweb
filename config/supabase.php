<?php
/**
 * Configuración de Supabase
 * Ibron Inmobiliaria
 */

// ============================================
// CREDENCIALES DE SUPABASE
// ============================================

define('SUPABASE_URL', 'https://ouqfqhajpkhfvietfhzp.supabase.co');
define('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im91cWZxaGFqcGtoZnZpZXRmaHpwIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzA2ODgxODYsImV4cCI6MjA4NjI2NDE4Nn0.3vllTUwE6YbIBunxarFBxe-0HXc5TYMbyVBuDOkCNmo');
define('SUPABASE_SERVICE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im91cWZxaGFqcGtoZnZpZXRmaHpwIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MDY4ODE4NiwiZXhwIjoyMDg2MjY0MTg2fQ.KiLkN4fKRjvmoKGR3VOEULMp6tjQkyu6TRDJLY1ES6M'); // Service Role Key - mantener privada

// ============================================
// CONFIGURACIÓN DE API
// ============================================

define('SUPABASE_API_URL', SUPABASE_URL . '/rest/v1');
define('SUPABASE_AUTH_URL', SUPABASE_URL . '/auth/v1');

// ============================================
// FUNCIONES HELPER DE SUPABASE
// ============================================

/**
 * Hacer petición GET a Supabase
 */
function supabase_get($table, $filters = [], $select = '*')
{
    $url = SUPABASE_API_URL . '/' . $table . '?select=' . urlencode($select);

    // Agregar filtros
    foreach ($filters as $key => $value) {
        $url .= '&' . urlencode($key) . '=' . urlencode($value);
    }

    $headers = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        return json_decode($response, true);
    }

    log_error('Supabase GET error', [
        'table' => $table,
        'http_code' => $http_code,
        'response' => $response
    ]);

    return false;
}

/**
 * Convierte un array de PostgreSQL (formato de texto {}) a un array de PHP
 */
function pg_array_to_php_array($postgresArray)
{
    if (!$postgresArray || $postgresArray === '{}')
        return [];
    if (is_array($postgresArray))
        return $postgresArray;

    $clean = trim($postgresArray, '{}');
    if (empty($clean))
        return [];

    // Manejar elementos entre comillas o separados por comas
    // Esta es una implementación simplificada para URLs de Supabase
    return str_getcsv($clean);
}

/**
 * Hacer petición POST a Supabase (Insert)
 */
function supabase_insert($table, $data)
{
    $url = SUPABASE_API_URL . '/' . $table;

    $headers = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 201) {
        return json_decode($response, true);
    }

    log_error('Supabase INSERT error', [
        'table' => $table,
        'http_code' => $http_code,
        'response' => $response
    ]);

    return false;
}

/**
 * Hacer petición PATCH a Supabase (Update)
 */
function supabase_update($table, $id, $data)
{
    $url = SUPABASE_API_URL . '/' . $table . '?id=eq.' . $id;

    // Usar SERVICE_KEY si está disponible para operaciones de escritura
    $key = (defined('SUPABASE_SERVICE_KEY') && SUPABASE_SERVICE_KEY !== 'TU_SERVICE_ROLE_KEY_AQUI')
        ? SUPABASE_SERVICE_KEY
        : SUPABASE_ANON_KEY;

    $headers = [
        'apikey: ' . $key,
        'Authorization: Bearer ' . $key,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        return json_decode($response, true);
    }

    log_error('Supabase UPDATE error', [
        'table' => $table,
        'id' => $id,
        'http_code' => $http_code,
        'response' => $response
    ]);

    return false;
}

/**
 * Hacer petición DELETE a Supabase
 */
function supabase_delete($table, $id)
{
    $url = SUPABASE_API_URL . '/' . $table . '?id=eq.' . $id;

    $headers = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 204) {
        return true;
    }

    log_error('Supabase DELETE error', [
        'table' => $table,
        'id' => $id,
        'http_code' => $http_code,
        'response' => $response
    ]);

    return false;
}

/**
 * Autenticar usuario con Supabase Auth
 */
function supabase_auth_login($email, $password)
{
    $url = SUPABASE_AUTH_URL . '/token?grant_type=password';

    $headers = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json'
    ];

    $data = [
        'email' => $email,
        'password' => $password
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        return json_decode($response, true);
    }

    return false;
}

/**
 * Verificar conexión a Supabase
 */
function test_supabase_connection()
{
    $url = SUPABASE_URL . '/rest/v1/';

    // Usar SERVICE_KEY si está disponible para borrado
    $key = (defined('SUPABASE_SERVICE_KEY') && SUPABASE_SERVICE_KEY !== 'TU_SERVICE_ROLE_KEY_AQUI')
        ? SUPABASE_SERVICE_KEY
        : SUPABASE_ANON_KEY;

    $headers = [
        'apikey: ' . $key,
        'Authorization: Bearer ' . $key
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_code === 200;
}

// ============================================
// SUPABASE STORAGE FUNCTIONS
// ============================================

/**
 * Subir archivo a Supabase Storage
 * @param string $bucket Nombre del bucket
 * @param string $file_path Ruta del archivo local
 * @param string $storage_path Ruta donde se guardará en el bucket
 * @return array|false Datos del archivo subido o false en caso de error
 */
function supabase_storage_upload($bucket, $file_path, $storage_path)
{
    if (!file_exists($file_path)) {
        log_error('File not found for upload', ['file_path' => $file_path]);
        return false;
    }

    $url = SUPABASE_URL . '/storage/v1/object/' . $bucket . '/' . $storage_path;

    $file_content = file_get_contents($file_path);
    $mime_type = mime_content_type($file_path);

    // Usar SERVICE_ROLE_KEY para storage operations (permite bypass de RLS)
    $key = (defined('SUPABASE_SERVICE_KEY') && SUPABASE_SERVICE_KEY !== 'TU_SERVICE_ROLE_KEY_AQUI')
        ? SUPABASE_SERVICE_KEY
        : SUPABASE_ANON_KEY;

    $headers = [
        'apikey: ' . $key,
        'Authorization: Bearer ' . $key,
        'Content-Type: ' . $mime_type,
        'x-upsert: true' // Sobrescribir si ya existe
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $file_content);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 || $http_code === 201) {
        return json_decode($response, true);
    }

    log_error('Supabase Storage UPLOAD error', [
        'bucket' => $bucket,
        'storage_path' => $storage_path,
        'http_code' => $http_code,
        'response' => $response
    ]);

    return false;
}

/**
 * Eliminar archivo de Supabase Storage
 * @param string $bucket Nombre del bucket
 * @param string $storage_path Ruta del archivo en el bucket
 * @return bool
 */
function supabase_storage_delete($bucket, $storage_path)
{
    $url = SUPABASE_URL . '/storage/v1/object/' . $bucket . '/' . $storage_path;

    // Usar SERVICE_KEY si está disponible, sino ANON_KEY
    $key = (defined('SUPABASE_SERVICE_KEY') && SUPABASE_SERVICE_KEY !== 'TU_SERVICE_ROLE_KEY_AQUI')
        ? SUPABASE_SERVICE_KEY
        : SUPABASE_ANON_KEY;

    $headers = [
        'apikey: ' . $key,
        'Authorization: Bearer ' . $key
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 || $http_code === 204) {
        return true;
    }

    log_error('Supabase Storage DELETE error', [
        'bucket' => $bucket,
        'storage_path' => $storage_path,
        'http_code' => $http_code,
        'response' => $response
    ]);

    return false;
}

/**
 * Obtener URL pública de un archivo en Supabase Storage
 * @param string $bucket Nombre del bucket
 * @param string $storage_path Ruta del archivo en el bucket
 * @return string URL pública
 */
function supabase_storage_get_public_url($bucket, $storage_path)
{
    return SUPABASE_URL . '/storage/v1/object/public/' . $bucket . '/' . $storage_path;
}

/**
 * Subir imagen de propiedad (helper específico)
 * @param array $file Array $_FILES['field_name']
 * @param string $property_id UUID de la propiedad
 * @param string $type 'main' o 'gallery'
 * @return string|false URL pública de la imagen o false
 */
function upload_property_image($file, $property_id, $type = 'main')
{
    // Validar archivo
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        log_error('Invalid image type', ['type' => $file['type']]);
        return false;
    }

    // Validar tamaño (máximo 5MB)
    if ($file['size'] > 5242880) {
        log_error('Image too large', ['size' => $file['size']]);
        return false;
    }

    // Generar nombre de archivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $timestamp = time();

    if ($type === 'main') {
        $storage_path = 'main/' . $property_id . '.' . $extension;
    } else {
        $storage_path = 'gallery/' . $property_id . '/' . $timestamp . '.' . $extension;
    }

    // Subir a Supabase Storage
    $result = supabase_storage_upload('property-images', $file['tmp_name'], $storage_path);

    if ($result) {
        return supabase_storage_get_public_url('property-images', $storage_path);
    }

    return false;
}

/**
 * Convertir array PHP a formato PostgreSQL array
 * @param array $array
 * @return string
 */
function php_array_to_pg_array($array)
{
    if (empty($array)) {
        return '{}';
    }
    $escaped = array_map(function ($item) {
        $val = str_replace(['\\', '"'], ['\\\\', '\"'], $item);
        return '"' . $val . '"';
    }, $array);
    return '{' . implode(',', $escaped) . '}';
}

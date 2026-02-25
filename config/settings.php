<?php
/**
 * Configuración Global del Sitio
 * Ibron Inmobiliaria
 */

// ============================================
// INFORMACIÓN DEL SITIO
// ============================================

define('SITE_NAME', 'Ibron Inmobiliaria, S.R.L.');
define('SITE_TAGLINE', 'TU MEJOR INVERSION');

// Auto-detectar protocolo
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https';
}
$host = $_SERVER['HTTP_HOST'];

// Detectar base_path relativo
$script_path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$project_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Si el script está en una subcarpeta (como api/), necesitamos subir niveles
// Pero SITE_URL se suele usar globalmente. Una forma más segura:
$current_dir = str_replace('\\', '/', dirname(__FILE__)); // config/
$root_dir = str_replace('\\', '/', dirname($current_dir)); // root/
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

$base_path = str_ireplace($doc_root, '', $root_dir);
$base_path = '/' . trim($base_path, '/');
if ($base_path === '/') $base_path = '';

define('SITE_URL', $protocol . '://' . $host . $base_path);

// ============================================
// INFORMACIÓN DE CONTACTO
// ============================================

define('CONTACT_EMAIL', 'ibroninmobiliaria@gmail.com');
define('CONTACT_PHONE', '829-352-6103');
define('CONTACT_PHONE_FORMATTED', '(829) 352-6103');
define('CONTACT_ADDRESS', 'República Dominicana');

// ============================================
// REDES SOCIALES
// ============================================

define('WHATSAPP_NUMBER', '18293526103'); // Formato internacional para WhatsApp
define('WHATSAPP_MESSAGE', 'Hola! Estoy interesado en sus propiedades');
define('INSTAGRAM_URL', 'https://www.instagram.com/ibroninmobiliaria?igsh=b2htYnJva3h3d3Y5');
define('FACEBOOK_URL', 'https://www.facebook.com/share/1AbPGHMKqY/');
define('YOUTUBE_URL', 'https://www.youtube.com/@ibroninmobiliaria8120');

// ============================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ============================================

define('PROPERTIES_PER_PAGE', 12);
define('FEATURED_PROPERTIES_LIMIT', 6);
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('CURRENCY_EXCHANGE_RATE', 60.0); // 1 USD = 60 DOP

// Cargar API keys desde archivo local (no incluido en git)
$local_keys_file = __DIR__ . '/local_keys.php';
if (file_exists($local_keys_file)) {
    require_once $local_keys_file;
}
if (!defined('GROQ_API_KEY')) {
    define('GROQ_API_KEY', ''); // Definir vacío si no hay archivo local
}

// ============================================
// RUTAS DEL SISTEMA
// ============================================

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// ============================================
// TIMEZONE
// ============================================

date_default_timezone_set('America/Santo_Domingo');

// ============================================
// SESIONES SEGURAS
// ============================================

// Configuración de sesiones seguras
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerar ID de sesión periodicamente para seguridad
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // Regenerar cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// ============================================
// FUNCIONES DE SEGURIDAD
// ============================================

/**
 * Sanitizar entrada de texto
 */
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validar email
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validar teléfono dominicano
 */
function validate_phone($phone)
{
    // Eliminar espacios y guiones
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

    // Validar formato dominicano (809, 829, 849)
    return preg_match('/^(\+?1)?[8][0-2,4][9]\d{7}$/', $phone);
}

/**
 * Generar token CSRF
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validar token CSRF
 */
function validate_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verificar si el usuario está autenticado
 */
function is_authenticated()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Requerir autenticación
 */
function require_auth()
{
    if (!is_authenticated()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit();
    }
}

/**
 * Cerrar sesión
 */
function logout_user()
{
    $_SESSION = [];
    session_destroy();
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit();
}

/**
 * Formatear precio con moneda (DOP por defecto o USD)
 */
function format_price($price, $currency = 'DOP')
{
    $prefix = ($currency === 'USD') ? 'US$ ' : 'RD$ ';
    return $prefix . number_format($price, 0, '.', ',');
}

/**
 * Formatear área
 */
function format_area($area)
{
    return number_format($area, 2) . ' m²';
}

/**
 * Obtener imagen por defecto si no existe
 */
function get_property_image($image_path)
{
    if (empty($image_path)) {
        // Usar un servicio más confiable que via.placeholder.com
        return 'https://placehold.co/600x400/333/gold?text=Ibron+Inmobiliaria';
    }
    return $image_path;
}

/**
 * Limitar texto a cierto número de palabras
 */
function limit_text($text, $word_limit = 20)
{
    $words = explode(' ', $text);
    if (count($words) > $word_limit) {
        return implode(' ', array_slice($words, 0, $word_limit)) . '...';
    }
    return $text;
}

/**
 * Protección contra XSS
 */
function escape_output($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generar slug amigable para URLs
 */
function generate_slug($string)
{
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

/**
 * Protección contra rate limiting (prevenir spam)
 */
function check_rate_limit($key, $max_attempts = 5, $time_window = 3600)
{
    $attempts_key = 'rate_limit_' . $key;

    if (!isset($_SESSION[$attempts_key])) {
        $_SESSION[$attempts_key] = [
            'count' => 0,
            'first_attempt' => time()
        ];
    }

    $data = $_SESSION[$attempts_key];

    // Reset si pasó el tiempo
    if (time() - $data['first_attempt'] > $time_window) {
        $_SESSION[$attempts_key] = [
            'count' => 1,
            'first_attempt' => time()
        ];
        return true;
    }

    // Incrementar contador
    $_SESSION[$attempts_key]['count']++;

    // Verificar si excedió el límite
    if ($data['count'] >= $max_attempts) {
        return false;
    }

    return true;
}

/**
 * Logging de errores personalizado
 */
function log_error($message, $context = [])
{
    $log_file = ROOT_PATH . '/logs/app.log';
    $log_dir = dirname($log_file);

    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $context_str = !empty($context) ? json_encode($context) : '';
    $log_message = "[$timestamp] $message $context_str" . PHP_EOL;

    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Validar y sanitizar datos de formulario
 */
function validate_form_data($data, $required_fields = [])
{
    $errors = [];

    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[$field] = "El campo $field es requerido";
        }
    }

    return $errors;
}

// ============================================
// HEADERS DE SEGURIDAD
// ============================================

// Prevenir clickjacking
header('X-Frame-Options: SAMEORIGIN');

// Prevenir MIME type sniffing
header('X-Content-Type-Options: nosniff');

// XSS Protection
header('X-XSS-Protection: 1; mode=block');

// Content Security Policy (ajustar según necesidades)
// header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");

// Referrer Policy
header('Referrer-Policy: strict-origin-when-cross-origin');

// ============================================
// AUTO-LOADER DE CONFIGURACIÓN
// ============================================

// Cargar configuración de Supabase si existe
$supabase_config = CONFIG_PATH . '/supabase.php';
if (file_exists($supabase_config)) {
    require_once $supabase_config;
}

/**
 * Descodificar una cadena base64 de una imagen
 */
function decode_base64_image($base64_string)
{
    if (empty($base64_string))
        return false;

    $parts = explode(',', $base64_string);
    if (count($parts) < 2)
        return false;

    $data = base64_decode($parts[1]);
    $type = "";

    if (preg_match('/^data:image\/(\w+);base64/', $parts[0], $type_match)) {
        $type = "image/" . $type_match[1];
    }

    return [
        'data' => $data,
        'type' => $type
    ];
}

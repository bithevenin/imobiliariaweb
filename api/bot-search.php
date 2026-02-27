<?php
// Desactivar visualización de errores para que no corrompan el JSON
ini_set('display_errors', 0);
error_reporting(0);

// Iniciar buffer de salida
ob_start();

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';

// Limpiar cualquier salida accidental de los includes
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$query = isset($input['query']) ? sanitize_input($input['query']) : '';
$lang = isset($input['lang']) ? sanitize_input($input['lang']) : 'es';

if (empty($query)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, dime qué estás buscando.',
        'suggestions' => ['Quiero una vivienda', 'Hablar con representante']
    ]);
    exit();
}

$response = [
    'success' => true,
    'type' => 'text',
    'message' => '',
    'properties' => [],
    'suggestions' => []
];

// --- 1. GESTIÓN DE MEMORIA (HISTORIAL) ---

// Inicializar historial si no existe
if (!isset($_SESSION['norvis_chat_history'])) {
    $_SESSION['norvis_chat_history'] = [];
}

// Agregar mensaje del usuario al historial
$_SESSION['norvis_chat_history'][] = ["role" => "user", "content" => $query];

// Mantener solo los últimos 10 mensajes (5 turnos) para no saturar el contexto
if (count($_SESSION['norvis_chat_history']) > 10) {
    $_SESSION['norvis_chat_history'] = array_slice($_SESSION['norvis_chat_history'], -10);
}

// --- 2. PREPARAR CONTEXTO PARA LA IA ---

$raw_properties = supabase_get('properties', ['status' => 'eq.Disponible', 'limit' => '10']);
$prop_context = "";
if ($raw_properties) {
    foreach ($raw_properties as $p) {
        $currency = 'DOP';
        if (!empty($p['features'])) {
            $features_arr = is_array($p['features']) ? $p['features'] : pg_array_to_php_array($p['features']);
            $features_arr = array_map('trim', $features_arr);
            if (in_array('USD', $features_arr)) {
                $currency = 'USD';
            }
        }
        $price = format_price($p['price'], $currency);
        $prop_context .= "- ID: {$p['id']}, Título: {$p['title']}, Tipo: {$p['type']}, Ciudad: {$p['ciudad']}, Sector: {$p['sector']}, Precio: {$price}\n";
    }
}

$agency_context = "
Nombre: Ibron Inmobiliaria, S.R.L.
Presidente: Norvi Rosario.
Años de experiencia: Más de 15 años.
Servicios: Asesoría en compra, venta e inversión de bienes raíces en República Dominicana.
Horario: Lun - Vie: 9:00 AM - 6:00 PM, Sáb: 9:00 AM - 2:00 PM.
Contacto: Tel: " . CONTACT_PHONE . ", Email: " . CONTACT_EMAIL . "
WhatsApp: " . WHATSAPP_NUMBER . "
Ubicación: " . CONTACT_ADDRESS . "
Redes Sociales:
- Instagram: " . INSTAGRAM_URL . "
- Facebook: " . FACEBOOK_URL . "
- YouTube: " . YOUTUBE_URL . "
Misión: Proporcionar servicios inmobiliarios de excelencia mediante un servicio personalizado y transparente.
";

$lang_names = [
    'es' => 'Español',
    'en' => 'Inglés',
    'fr' => 'Francés',
    'pt' => 'Portugués',
    'it' => 'Italiano',
    'de' => 'Alemán',
    'ru' => 'Ruso',
    'zh' => 'Chino',
    'ja' => 'Japonés',
    'ar' => 'Árabe',
    'hi' => 'Hindi',
    'nl' => 'Holandés',
    'tr' => 'Turco'
];
$target_lang_name = $lang_names[substr($lang, 0, 2)] ?? 'Español';

$system_prompt = "
Eres 'Norvis', el asistente virtual e inteligente de Ibron Inmobiliaria, y sobre todo, eres un NEGOCIANTE POR EXCELENCIA. 
Tu objetivo no es solo informar, sino CERRAR ventas y captar clientes altamente interesados.

IMPORTANTE: DEBES RESPONDER SIEMPRE EN EL IDIOMA: {$target_lang_name}.

PERSONALIDAD:
- Profesional, persuasivo, amable y con gran visión comercial.
- Si detectas que el cliente tiene intención de comprar, invertir, o pregunta por precios y visitas, actúa como un cerrador de ventas experto.
...
";

// --- 3. LLAMADA A GROQ API ---

function call_groq($system_prompt, $history) {
    $api_key = GROQ_API_KEY;
    $url = "https://api.groq.com/openai/v1/chat/completions";

    $messages = [["role" => "system", "content" => $system_prompt]];
    foreach ($history as $msg) {
        $messages[] = $msg;
    }

    $data = [
        "model" => "llama-3.3-70b-versatile",
        "messages" => $messages,
        "temperature" => 0.4, // Bajamos más la temperatura para evitar disparos falsos del trigger
        "max_tokens" => 800
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);

    $res = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => curl_error($ch)];
    }
    curl_close($ch);

    return json_decode($res, true);
}

$ai_res = call_groq($system_prompt, $_SESSION['norvis_chat_history']);

if (isset($ai_res['error']) || !isset($ai_res['choices'][0]['message']['content'])) {
    $err_msg = isset($ai_res['error']['message']) ? $ai_res['error']['message'] : 'Error desconocido';
    $response['message'] = "Lo siento, mi conexión con la nube se ha interrumpido brevemente. ¿Podrías repetirme eso?";
    echo json_encode($response);
    exit();
}

// --- 4. PROCESAR RESPUESTA Y TRIGGERS ---

$ai_text = $ai_res['choices'][0]['message']['content'];
$low_query = mb_strtolower(trim($query));

// --- VALIDACIÓN PROGRAMÁTICA DE INTENCIÓN (SEGURIDAD EXTRA) ---
$buy_keywords = ['comprar', 'adquirir', 'invertir', 'interesado en compra', 'lo compro', 'precio final', 'hacer oferta', 'apartar', 'reservar'];
$rep_keywords = ['representante', 'asesor', 'humano', 'persona', 'hablar con alguien', 'contactar', 'teléfono', 'llamada', 'norvi'];

$has_intent = false;
foreach (array_merge($buy_keywords, $rep_keywords) as $kw) {
    if (strpos($low_query, $kw) !== false) {
        $has_intent = true;
        break;
    }
}

// --- DETECCIÓN DE TRIGGER PARA WHATSAPP ---
// Solo permitimos el trigger si la IA lo incluyó Y detectamos intención en el mensaje del usuario
if (strpos($ai_text, '[TRIGGER_WHATSAPP]') !== false) {
    if ($has_intent) {
        $response['redirect_whatsapp'] = true;
        $response['whatsapp_url'] = "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . urlencode("Hola Norvi Rosario, estoy hablando con Norvis y me interesa mucho una propiedad en Ibron Inmobiliaria. Quisiera más información para concretar.");
    }
    $ai_text = str_replace('[TRIGGER_WHATSAPP]', '', $ai_text);
}

// --- DETECCIÓN DE NAVEGACIÓN INTERNA ---
if (preg_match('/\[NAVIGATE:([a-zA-Z0-9-]+)\]/', $ai_text, $matches)) {
    $prop_id = $matches[1];
    $response['redirect_url'] = SITE_URL . "/property-detail.php?id=" . $prop_id;
    $ai_text = preg_replace('/\[NAVIGATE:[a-zA-Z0-9-]+\]/', '', $ai_text);
}

$response['message'] = trim($ai_text);

// --- GUARDIA DE SEGURIDAD PARA SALUDOS ---
$greetings = ['hola', 'buen día', 'buenos días', 'buenas tardes', 'buenas noches', 'saludos', 'hola norvis'];
if (in_array($low_query, $greetings) || mb_strlen($low_query) < 4) {
    $response['redirect_whatsapp'] = false;
    unset($response['whatsapp_url']);
    $response['redirect_url'] = null;
    unset($response['redirect_url']);
}

// Agregar respuesta de la IA al historial
$_SESSION['norvis_chat_history'][] = ["role" => "assistant", "content" => $ai_text];

// --- 4. DETECTAR PROPIEDADES EN LA RESPUESTA ---
$found_props = [];
if ($raw_properties) {
    foreach ($raw_properties as $p) {
        if (strpos($ai_text, (string)$p['id']) !== false || strpos(mb_strtolower($ai_text), mb_strtolower($p['title'])) !== false) {
            
            $currency = 'DOP';
            if (!empty($p['features'])) {
                $features_arr = is_array($p['features']) ? $p['features'] : pg_array_to_php_array($p['features']);
                $features_arr = array_map('trim', $features_arr);
                if (in_array('USD', $features_arr)) {
                    $currency = 'USD';
                }
            }

            $img = $p['image_main'] ?? 'assets/img/placeholder.jpg';
            if (strpos($img, 'http') === false) {
                $img = SITE_URL . '/' . ltrim($img, '/');
            }

            $found_props[] = [
                'id' => $p['id'],
                'title' => $p['title'],
                'type' => $p['type'] ?? 'Propiedad',
                'bedrooms' => $p['bedrooms'] ?? 0,
                'bathrooms' => $p['bathrooms'] ?? 0,
                'area' => $p['area'] ?? 0,
                'price' => format_price($p['price'], $currency),
                'sector' => $p['sector'] ?? '',
                'ciudad' => $p['ciudad'] ?? '',
                'location' => ($p['sector'] ?? '') . ' ' . ($p['ciudad'] ?? ''),
                'image' => $img,
                'url' => SITE_URL . '/property-detail.php?id=' . $p['id'],
                'whatsapp' => "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . urlencode("Hola, me interesa esta propiedad: " . $p['title'] . " (ID: " . $p['id'] . ")")
            ];
            
            if (count($found_props) >= 3) break;
        }
    }
}

if (count($found_props) > 0) {
    $response['type'] = 'properties';
    $response['properties'] = $found_props;
} else {
    $response['suggestions'] = ['Hablar con representante', 'Ver más opciones'];
}

echo json_encode($response);

<?php
/**
 * Sistema de Analítica de Propiedades - Ibron Inmobiliaria
 * Maneja el rastreo de vistas únicas por propiedad
 */

require_once __DIR__ . '/supabase.php';

/**
 * Registra una vista para una propiedad específica si es un visitante nuevo o único del día
 * @param string $property_id UUID de la propiedad
 * @return bool
 */
function track_property_view($property_id) {
    if (!$property_id) return false;

    // 1. Identificar al visitante (Cookie persistente + Hash temporal)
    $visitor_cookie_name = 'ibron_vid';
    if (!isset($_COOKIE[$visitor_cookie_name])) {
        $visitor_id = bin2hex(random_bytes(16));
        setcookie($visitor_cookie_name, $visitor_id, time() + (86400 * 365), "/"); // 1 año
    } else {
        $visitor_id = $_COOKIE[$visitor_cookie_name];
    }

    // 2. Crear un fingerprint del dispositivo (IP + User Agent + Sal diaria)
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $day_salt = date('Y-m-d'); 
    $fingerprint = hash('sha256', $visitor_id . $ip . $ua . $day_salt);

    // 3. Verificar si ya se contó esta vista hoy en la SESIÓN para evitar hits rápidos
    $session_key = "viewed_prop_" . $property_id;
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (isset($_SESSION[$session_key])) {
        return false; // Ya visto en esta sesión
    }

    // 4. Consultar Supabase para ver si este fingerprint ya registró vista hoy para ESTA propiedad
    // Nota: Esto es opcional si confiamos en la sesión, pero lo hace más "real" entre dispositivos con misma IP
    $today_start = date('Y-m-d') . 'T00:00:00Z';
    $existing = supabase_get('property_analytics', [
        'property_id' => 'eq.' . $property_id,
        'viewer_id' => 'eq.' . $fingerprint,
        'created_at' => 'gte.' . $today_start
    ]);

    if (!empty($existing)) {
        $_SESSION[$session_key] = true;
        return false; // Ya registrado en DB hoy
    }

    // 5. Registrar la vista en la tabla de analíticas
    $data = [
        'property_id' => $property_id,
        'viewer_id' => $fingerprint,
        'created_at' => date('c')
    ];

    $result = supabase_insert('property_analytics', $data);

    if ($result) {
        // Marca en sesión para no re-consultar en este request/sesión
        $_SESSION[$session_key] = true;

        // 6. Actualizar el contador acumulado en la tabla properties (para velocidad de UI)
        // Primero obtenemos el valor actual
        $prop_data = supabase_get('properties', ['id' => 'eq.' . $property_id], 'views');
        if (!empty($prop_data)) {
            $current_views = (int)($prop_data[0]['views'] ?? 0);
            supabase_update('properties', $property_id, ['views' => $current_views + 1]);
        }
        return true;
    }

    return false;
}

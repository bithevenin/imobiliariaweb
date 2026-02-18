<?php
/**
 * Contador de Visitas - Ibron Inmobiliaria
 * Sistema simple de conteo de visitas para el sitio web
 */

// Archivo donde se guardará el contador
$counter_file = __DIR__ . '/../uploads/visitor_count.txt';
$daily_file = __DIR__ . '/../uploads/daily_visitors.json';

// Asegurar que el directorio existe
if (!file_exists(dirname($counter_file))) {
    mkdir(dirname($counter_file), 0755, true);
}

/**
 * Incrementar contador de visitas
 */
function increment_visitor_count() {
    global $counter_file, $daily_file;
    
    // Verificar si ya visitó en esta sesión
    if (!isset($_SESSION['visitor_counted'])) {
        
        // Leer contador actual
        $count = 0;
        if (file_exists($counter_file)) {
            $count = (int)file_get_contents($counter_file);
        }
        
        // Incrementar
        $count++;
        
        // Guardar nuevo contador
        file_put_contents($counter_file, $count);
        
        // Marcar como contado en esta sesión
        $_SESSION['visitor_counted'] = true;
        $_SESSION['visitor_count_value'] = $count;
        
        // Registrar visita diaria
        track_daily_visitor();
    }
}

/**
 * Rastrear visitantes únicos por día
 */
function track_daily_visitor() {
    global $daily_file;
    
    $today = date('Y-m-d');
    $visitor_ip = get_visitor_ip();
    
    // Leer datos existentes
    $daily_data = [];
    if (file_exists($daily_file)) {
        $daily_data = json_decode(file_get_contents($daily_file), true) ?: [];
    }
    
    // Limpiar días antiguos (mantener solo últimos 30 días)
    $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
    foreach ($daily_data as $date => $visitors) {
        if ($date < $thirty_days_ago) {
            unset($daily_data[$date]);
        }
    }
    
    // Inicializar día actual si no existe
    if (!isset($daily_data[$today])) {
        $daily_data[$today] = [];
    }
    
    // Agregar visitante único (por IP)
    if (!in_array($visitor_ip, $daily_data[$today])) {
        $daily_data[$today][] = $visitor_ip;
    }
    
    // Guardar datos actualizados
    file_put_contents($daily_file, json_encode($daily_data, JSON_PRETTY_PRINT));
}

/**
 * Obtener IP del visitante (compatible con proxies)
 */
function get_visitor_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    // Hash la IP por privacidad
    return hash('sha256', $ip . date('Y-m-d'));
}

/**
 * Obtener contador total de visitas
 */
function get_total_visits() {
    global $counter_file;
    
    if (file_exists($counter_file)) {
        return (int)file_get_contents($counter_file);
    }
    
    return 0;
}

/**
 * Obtener visitantes únicos de hoy
 */
function get_today_unique_visitors() {
    global $daily_file;
    
    $today = date('Y-m-d');
    
    if (file_exists($daily_file)) {
        $daily_data = json_decode(file_get_contents($daily_file), true) ?: [];
        return isset($daily_data[$today]) ? count($daily_data[$today]) : 0;
    }
    
    return 0;
}

/**
 * Obtener visitantes únicos de los últimos 7 días
 */
function get_week_unique_visitors() {
    global $daily_file;
    
    if (!file_exists($daily_file)) {
        return 0;
    }
    
    $daily_data = json_decode(file_get_contents($daily_file), true) ?: [];
    $unique_visitors = [];
    
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        if (isset($daily_data[$date])) {
            $unique_visitors = array_merge($unique_visitors, $daily_data[$date]);
        }
    }
    
    return count(array_unique($unique_visitors));
}

/**
 * Mostrar badge del contador
 */
function display_visitor_badge($show_today = true) {
    $total = get_total_visits();
    $today = get_today_unique_visitors();
    
    echo '<div class="visitor-counter">';
    echo '<i class="fas fa-eye me-2"></i>';
    echo '<span class="total-visits">' . number_format($total) . ' visitas</span>';
    
    if ($show_today) {
        echo '<span class="today-visits ms-3">';
        echo '<i class="fas fa-users me-1"></i>';
        echo $today . ' hoy';
        echo '</span>';
    }
    
    echo '</div>';
}

// Incrementar contador automáticamente al incluir este archivo
increment_visitor_count();

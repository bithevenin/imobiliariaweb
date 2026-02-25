<?php
/**
 * Contador de Visitas Mensual (DB) - Ibron Inmobiliaria
 * Extrae datos reales de Supabase con reinicio mensual automático
 */

require_once __DIR__ . '/../config/supabase.php';

/**
 * Obtener visitantes únicos del mes actual
 */
function get_monthly_visits() {
    $month_start = date('Y-m-01') . 'T00:00:00Z';
    $data = supabase_get('property_analytics', [
        'created_at' => 'gte.' . $month_start
    ]);
    
    if ($data === false) return 0;
    
    // Contar IDs de visitantes únicos en el mes
    $viewers = array_column($data, 'viewer_id');
    return count(array_unique($viewers));
}

/**
 * Obtener visitantes de hoy (Vistas únicas registradas en analíticas)
 */
function get_today_unique_visitors() {
    $today_start = date('Y-m-d') . 'T00:00:00Z';
    $data = supabase_get('property_analytics', [
        'created_at' => 'gte.' . $today_start
    ]);
    
    if ($data === false) return 0;
    
    // Contar IDs de visitantes únicos hoy
    $viewers = array_column($data, 'viewer_id');
    return count(array_unique($viewers));
}

/**
 * Mostrar badge del contador
 */
function display_visitor_badge($show_today = true) {
    // Ahora 'total' es realmente el conteo del mes actual
    $monthly_total = get_monthly_visits();
    $today = get_today_unique_visitors();
    
    echo '<div class="visitor-counter d-flex align-items-center justify-content-end">';
    echo '  <div class="me-3">';
    echo '    <i class="fas fa-eye text-gold me-1" title="Vistas este mes"></i>';
    echo '    <span class="small fw-bold">' . number_format($monthly_total) . ' este mes</span>';
    echo '  </div>';
    
    if ($show_today) {
        echo '  <div>';
        echo '    <i class="fas fa-users text-gold me-1" title="Visitantes hoy"></i>';
        echo '    <span class="small fw-bold">' . $today . ' hoy</span>';
        echo '  </div>';
    }
    
    echo '</div>';
}

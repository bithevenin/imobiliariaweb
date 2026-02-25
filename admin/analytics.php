<?php
/**
 * Panel de Analíticas - Ibron Inmobiliaria
 * Muestra estadísticas detalladas de visitas por propiedad
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth();

$page_title = 'Analíticas';

// 1. Obtener Periodo (Últimos 30 días por defecto)
$days_to_show = isset($_GET['period']) && $_GET['period'] === 'week' ? 7 : 30;
$start_date = date('Y-m-d', strtotime("-$days_to_show days")) . 'T00:00:00Z';

// 2. Obtener Datos de Analítica desde Supabase
$analytics_data = supabase_get('property_analytics', [
    'created_at' => 'gte.' . $start_date,
    'order' => 'created_at.asc'
]);

if ($analytics_data === false) $analytics_data = [];

// 3. Obtener Propiedades para mapear nombres
$properties = supabase_get('properties', [], 'id,title,views');
$prop_map = [];
foreach ($properties as $p) {
    $prop_map[$p['id']] = $p['title'];
}

// 4. Procesar Datos para Gráficos
$daily_stats = [];
for ($i = $days_to_show - 1; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $daily_stats[$date] = 0;
}

$property_performance = [];
foreach ($analytics_data as $row) {
    $date = date('Y-m-d', strtotime($row['created_at']));
    if (isset($daily_stats[$date])) {
        $daily_stats[$date]++;
    }
    
    $pid = $row['property_id'];
    if (!isset($property_performance[$pid])) {
        $property_performance[$pid] = 0;
    }
    $property_performance[$pid]++;
}

// Ordenar propiedades por desempeño en el periodo
arsort($property_performance);

// Preparar etiquetas y datos para Chart.js
$chart_labels = array_keys($daily_stats);
$chart_values = array_values($daily_stats);

// Resumen General
$total_period_views = count($analytics_data);
$avg_daily_views = $days_to_show > 0 ? round($total_period_views / $days_to_show, 1) : 0;
$top_property_id = !empty($property_performance) ? array_key_first($property_performance) : null;
$top_property_name = $top_property_id ? ($prop_map[$top_property_id] ?? 'Desconocida') : 'N/A';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=1.0">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=1.0">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --sidebar-width: 250px; }
        .sidebar { width: var(--sidebar-width); min-height: 100vh; background: linear-gradient(180deg, #2a2a2a 0%, #3d3d3d 100%); position: fixed; left: 0; top: 0; z-index: 1000; transition: all 0.3s ease; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: block; padding: 12px 25px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(212,167,69,0.1); border-left-color: var(--color-gold); color: white; }
        .main-content { margin-left: var(--sidebar-width); transition: all 0.3s ease; width: calc(100% - var(--sidebar-width)); padding: 2rem; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .stat-card { padding: 1.5rem; text-align: center; }
        .stat-val { font-size: 2rem; font-weight: 700; color: var(--color-gold); }
        @media (max-width: 768px) { .sidebar { left: -250px; } .main-content { margin-left: 0; width: 100%; } .sidebar.active { left: 0; } }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header text-center">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" style="max-width: 120px;" class="mb-2">
                <p class="text-white-50 small mb-0">Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="analytics.php" class="active"><i class="fas fa-chart-line me-2"></i>Analíticas</a>
                <a href="properties-manage.php"><i class="fas fa-building me-2"></i>Propiedades</a>
                <a href="property-form.php"><i class="fas fa-plus-circle me-2"></i>Nueva Propiedad</a>
                <a href="messages.php"><i class="fas fa-envelope me-2"></i>Mensajes</a>
                <hr class="mx-3 bg-white-50">
                <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Analíticas de Propiedades</h2>
                <div class="btn-group">
                    <a href="?period=week" class="btn <?php echo $days_to_show === 7 ? 'btn-gold' : 'btn-outline-dark'; ?> shadow-sm">Esta Semana</a>
                    <a href="?period=month" class="btn <?php echo $days_to_show === 30 ? 'btn-gold' : 'btn-outline-dark'; ?> shadow-sm">Este Mes</a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <p class="text-muted small mb-1">Vistas en el Periodo</p>
                        <div class="stat-val"><?php echo number_format($total_period_views); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <p class="text-muted small mb-1">Promedio Diario</p>
                        <div class="stat-val"><?php echo $avg_daily_views; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <p class="text-muted small mb-1">Propiedad Estrella</p>
                        <div class="small fw-bold text-truncate" title="<?php echo $top_property_name; ?>">
                            <?php echo $top_property_name; ?>
                        </div>
                        <div class="stat-val" style="font-size: 1.2rem;"><?php echo !empty($property_performance) ? current($property_performance) : 0; ?> vistas</div>
                    </div>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <h5 class="mb-4">Tendencia de Visitas</h5>
                <canvas id="viewChart" style="max-height: 350px;"></canvas>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Rendimiento por Propiedad</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                            <th class="ps-4">Propiedad</th>
                                            <th class="text-center">Vistas (Periodo)</th>
                                            <th class="text-center">Vistas Totales</th>
                                            <th class="text-end pe-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($property_performance)): ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted">No hay datos en este periodo</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($property_performance as $pid => $views): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold"><?php echo $prop_map[$pid] ?? 'Propiedad eliminada'; ?></td>
                                                <td class="text-center"><span class="badge bg-primary rounded-pill"><?php echo $views; ?></span></td>
                                                <td class="text-center text-muted"><?php 
                                                    // Buscar vista total en el array original masivo de propiedades
                                                    $total_ever = 0;
                                                    foreach($properties as $ptot) { if($ptot['id'] == $pid) { $total_ever = $ptot['views']; break; } }
                                                    echo number_format($total_ever);
                                                ?></td>
                                                <td class="text-end pe-4">
                                                    <a href="../property-detail.php?id=<?php echo $pid; ?>" target="_blank" class="btn btn-sm btn-outline-dark">Ver</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('viewChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Visitas Únicas',
                    data: <?php echo json_encode($chart_values); ?>,
                    borderColor: '#d4a745',
                    backgroundColor: 'rgba(212, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#d4a745'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { drawBorder: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>

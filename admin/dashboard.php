<?php
/**
 * Admin Dashboard - Ibron Inmobiliaria
 * Panel principal de administración
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth(); // Requerir autenticación

$page_title = 'Dashboard';

// Obtener todas las propiedades desde Supabase
$all_properties = supabase_get('properties', []);

// Obtener todos los mensajes desde Supabase  
$all_messages = supabase_get('contact_messages', []);

// Inicializar estadísticas por defecto
$stats = [
    'total_properties' => 0,
    'available_properties' => 0,
    'sold_properties' => 0,
    'reserved_properties' => 0,
    'total_messages' => 0,
    'unread_messages' => 0,
    'total_visits' => 0,
    'storage_percent' => 0,
    'used_storage' => 0,
    'estimated_photos' => 0
];

// Calcular estadísticas si hay datos
if ($all_properties !== false) {
    $stats['total_properties'] = count($all_properties);
    $stats['available_properties'] = count(array_filter($all_properties, fn($p) => ($p['status'] ?? '') === 'Disponible'));
    $stats['sold_properties'] = count(array_filter($all_properties, fn($p) => ($p['status'] ?? '') === 'Vendida'));
    $stats['reserved_properties'] = count(array_filter($all_properties, fn($p) => ($p['status'] ?? '') === 'Reservada'));
    $stats['total_visits'] = array_sum(array_column($all_properties, 'views'));
    
    // Almacenamiento Estimado - Conteo Preciso
    $estimated_photos = 0;
    foreach ($all_properties as $p) {
        if (!empty($p['image_main'])) {
            $estimated_photos++;
        }
        
        if (!empty($p['image_gallery'])) {
            $gal = is_array($p['image_gallery']) ? $p['image_gallery'] : pg_array_to_php_array($p['image_gallery']);
            foreach ($gal as $img) {
                if (!empty(trim($img))) {
                    $estimated_photos++;
                }
            }
        }
    }
    
    $total_storage_mb = 1024;
    $used_storage_mb = $estimated_photos * 0.5;
    $stats['storage_percent'] = min(100, round(($used_storage_mb / $total_storage_mb) * 100, 1));
    $stats['used_storage'] = $used_storage_mb;
    $stats['estimated_photos'] = $estimated_photos;
}

if ($all_messages !== false) {
    $stats['total_messages'] = count($all_messages);
    $stats['unread_messages'] = count(array_filter($all_messages, fn($m) => ($m['status'] ?? '') === 'new'));
} else {
    log_error('Failed to fetch dashboard stats from Supabase');
}
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
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/favicon.ico">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #2a2a2a 0%, #3d3d3d 100%);
            padding: 0;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(212, 167, 69, 0.1);
            border-left-color: var(--color-gold);
            color: white;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }

        .mobile-header {
            display: none;
            background: white;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .mobile-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 998;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }

        .stat-card {
            border-radius: 12px;
            padding: 20px;
            background: white;
            border: none;
            box-shadow: var(--shadow-sm);
            height: 100%;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .text-gold { color: var(--color-gold); }
        .bg-gold-light { background: rgba(212, 167, 69, 0.1); }
    </style>
</head>

<body class="bg-light">

    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid p-0">
        <!-- Header Móvil -->
        <div class="mobile-header">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" style="height: 30px; width: auto;">
            <button class="btn btn-dark" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar">
                <div class="sidebar-header text-center">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" style="max-width: 120px; height: auto;" class="mb-2">
                    <p class="text-white-50 small mb-0">Admin Panel</p>
                </div>

                <div class="sidebar-menu">
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="<?php echo $page_title === 'Dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php">
                        <i class="fas fa-building me-2"></i>Propiedades
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/property-form.php">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Propiedad
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/messages.php">
                        <i class="fas fa-envelope me-2"></i>Mensajes
                        <?php if ($stats['unread_messages'] > 0): ?>
                            <span class="badge bg-danger ms-2"><?php echo $stats['unread_messages']; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Sitio Web
                    </a>
                    <hr class="mx-3 bg-white-50">
                    <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </a>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content p-3 p-md-4">
                <!-- Header (Escritorio) -->
                <div class="admin-header d-none d-md-block mb-4 bg-white p-3 rounded shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0 fw-bold h4">Dashboard</h2>
                            <p class="text-muted mb-0 small">Bienvenido de nuevo, <?php echo escape_output($_SESSION['username']); ?></p>
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?php echo date('d \d\e F, Y'); ?>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Rápidas -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small mb-1">Total Propiedades</p>
                                    <h3 class="mb-0 fw-bold"><?php echo $stats['total_properties']; ?></h3>
                                </div>
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary d-none d-sm-flex">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small mb-1">Disponibles</p>
                                    <h3 class="mb-0 fw-bold text-success"><?php echo $stats['available_properties']; ?></h3>
                                </div>
                                <div class="stat-icon bg-success bg-opacity-10 text-success d-none d-sm-flex">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small mb-1">Mensajes</p>
                                    <h3 class="mb-0 fw-bold text-gold"><?php echo $stats['unread_messages']; ?></h3>
                                </div>
                                <div class="stat-icon bg-gold-light text-gold d-none d-sm-flex">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small mb-1">Visitas</p>
                                    <h3 class="mb-0 fw-bold text-primary"><?php echo number_format($stats['total_visits']); ?></h3>
                                </div>
                                <div class="stat-icon bg-info bg-opacity-10 text-info d-none d-sm-flex">
                                    <i class="fas fa-eye"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sistema y Acciones -->
                <div class="row g-3 mb-4">
                    <!-- Estado del Sistema -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body p-3">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3">
                                    <i class="fas fa-server me-2"></i>Estado del Sistema
                                </h6>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Almacenamiento (1GB)</small>
                                        <span class="badge <?php echo $stats['storage_percent'] > 80 ? 'bg-danger' : 'bg-success'; ?> p-1">
                                            <?php echo $stats['storage_percent']; ?>%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 8px; border-radius: 4px;">
                                        <div class="progress-bar <?php echo $stats['storage_percent'] > 80 ? 'bg-danger' : 'bg-success'; ?>" 
                                             role="progressbar" style="width: <?php echo $stats['storage_percent']; ?>%"></div>
                                    </div>
                                </div>

                                <div class="list-group list-group-flush small">
                                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span><i class="fas fa-image me-2 text-muted"></i>Fotos Totales</span>
                                        <span class="fw-bold"><?php echo $stats['estimated_photos']; ?></span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span><i class="fas fa-hdd me-2 text-muted"></i>Espacio Usado</span>
                                        <span class="fw-bold"><?php echo round($stats['used_storage'], 1); ?> MB</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span><i class="fas fa-lock me-2 text-muted"></i>Rol</span>
                                        <span class="badge bg-gold"><?php echo ucfirst($_SESSION['role']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body p-3">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3">
                                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6 col-xl-3">
                                        <a href="<?php echo SITE_URL; ?>/admin/property-form.php" class="btn btn-primary w-100 py-3 d-flex flex-column align-items-center border-0 shadow-sm">
                                            <i class="fas fa-plus-circle mb-2 fa-lg"></i>
                                            <span class="small fw-bold">Nueva Propiedad</span>
                                        </a>
                                    </div>
                                    <div class="col-6 col-xl-3">
                                        <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php" class="btn btn-dark w-100 py-3 d-flex flex-column align-items-center border-0 shadow-sm">
                                            <i class="fas fa-tasks mb-2 fa-lg"></i>
                                            <span class="small fw-bold">Gestionar</span>
                                        </a>
                                    </div>
                                    <div class="col-6 col-xl-3">
                                        <a href="<?php echo SITE_URL; ?>/admin/messages.php" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center shadow-sm">
                                            <i class="fas fa-envelope mb-2 fa-lg"></i>
                                            <span class="small fw-bold">Mensajes</span>
                                        </a>
                                    </div>
                                    <div class="col-6 col-xl-3">
                                        <a href="<?php echo SITE_URL; ?>/index.php" target="_blank" class="btn btn-info text-white w-100 py-3 d-flex flex-column align-items-center border-0 shadow-sm">
                                            <i class="fas fa-external-link-alt mb-2 fa-lg"></i>
                                            <span class="small fw-bold">Sitio Web</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimas Propiedades -->
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold h6"><i class="fas fa-building text-gold me-2"></i>Propiedades Recientes</h5>
                                <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php" class="btn btn-sm btn-outline-dark px-3">
                                    Ver Todas
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-light border shadow-sm small">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    Mostrando datos reales desde <strong>Supabase</strong>.
                                </div>
                                <!-- Aquí se puede incluir una tabla rápida -->
                                <p class="text-muted small text-center my-4">Aquí aparecerá un resumen de tus últimas publicaciones.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar Móvil
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
        }
    </script>
</body>

</html>
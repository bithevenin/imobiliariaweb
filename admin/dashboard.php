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

// Calcular estadísticas
if ($all_properties && $all_messages) {
    $stats = [
        'total_properties' => count($all_properties),
        'available_properties' => count(array_filter($all_properties, fn($p) => $p['status'] === 'Disponible')),
        'sold_properties' => count(array_filter($all_properties, fn($p) => $p['status'] === 'Vendida')),
        'reserved_properties' => count(array_filter($all_properties, fn($p) => $p['status'] === 'Reservada')),
        'total_messages' => count($all_messages),
        'unread_messages' => count(array_filter($all_messages, fn($m) => $m['status'] === 'new'))
    ];
} else {
    // Fallback si falla la conexión
    $stats = [
        'total_properties' => 0,
        'available_properties' => 0,
        'sold_properties' => 0,
        'reserved_properties' => 0,
        'total_messages' => 0,
        'unread_messages' => 0
    ];
    log_error('Failed to fetch dashboard stats from Supabase');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title; ?> - Admin
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2a2a2a 0%, #3d3d3d 100%);
            padding: 0;
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

        .stat-card {
            border-radius: 12px;
            padding: 25px;
            background: white;
            border: none;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .admin-header {
            background: white;
            padding: 15px 0;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 col-lg-2 d-md-block sidebar">
                <div class="sidebar-header text-center">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" style="max-width: 120px;"
                        class="mb-2">
                    <p class="text-white-50 small mb-0">Admin Panel</p>
                </div>

                <div class="sidebar-menu">
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="active">
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
                            <span class="badge bg-danger ms-2">
                                <?php echo $stats['unread_messages']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Sitio Web
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </a>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 col-lg-10 ms-sm-auto px-md-4">
                <!-- Header -->
                <div class="admin-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">Dashboard</h2>
                            <p class="text-muted mb-0">Bienvenido,
                                <?php echo escape_output($_SESSION['username']); ?>
                            </p>
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <?php echo date('l, d \d\e F \d\e Y'); ?>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-2">Total Propiedades</p>
                                    <h3 class="mb-0">
                                        <?php echo $stats['total_properties']; ?>
                                    </h3>
                                </div>
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-2">Disponibles</p>
                                    <h3 class="mb-0 text-success">
                                        <?php echo $stats['available_properties']; ?>
                                    </h3>
                                </div>
                                <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-2">Vendidas</p>
                                    <h3 class="mb-0 text-danger">
                                        <?php echo $stats['sold_properties']; ?>
                                    </h3>
                                </div>
                                <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                    <i class="fas fa-tag"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-2">Mensajes Nuevos</p>
                                    <h3 class="mb-0 text-gold">
                                        <?php echo $stats['unread_messages']; ?>
                                    </h3>
                                </div>
                                <div class="stat-icon"
                                    style="background: rgba(212, 167, 69, 0.1); color: var(--color-gold);">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-bolt text-gold me-2"></i>Acciones Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <a href="<?php echo SITE_URL; ?>/admin/property-form.php"
                                            class="btn btn-primary w-100 py-3">
                                            <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                                            Agregar Propiedad
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php"
                                            class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-edit fa-2x mb-2 d-block"></i>
                                            Gestionar Propiedades
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?php echo SITE_URL; ?>/admin/messages.php"
                                            class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-envelope fa-2x mb-2 d-block"></i>
                                            Ver Mensajes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle text-gold me-2"></i>Sistema</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Base de Datos</small>
                                    <strong>Supabase</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Última Sesión</small>
                                    <strong>
                                        <?php echo date('d/m/Y H:i', $_SESSION['login_time']); ?>
                                    </strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Rol</small>
                                    <span class="badge bg-gold">
                                        <?php echo ucfirst($_SESSION['role']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimas Propiedades -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-building text-gold me-2"></i>Últimas Propiedades</h5>
                                <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php"
                                    class="btn btn-sm btn-outline-gold">
                                    Ver Todas
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Modo de desarrollo:</strong> Conecta a Supabase para ver datos reales de
                                    propiedades.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>

</body>

</html>
<?php
/**
 * Mensajes de Contacto - Ibron Inmobiliaria
 * Visualización y gestión de mensajes recibidos desde el formulario de contacto
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth(); // Requerir autenticación

$page_title = 'Mensajes';

// Obtener todos los mensajes desde Supabase
$messages = supabase_get('contact_messages', ['order' => 'created_at.desc']);

if ($messages === false) {
    $messages = [];
    log_error('Failed to fetch messages from Supabase');
}

// Contador de mensajes no leídos
$unread_count = count(array_filter($messages, fn($m) => ($m['status'] ?? '') === 'new'));
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

        .message-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .message-card.unread {
            border-left-color: var(--color-gold);
            background: #fffcf5;
        }

        .message-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .message-meta-item {
            font-size: 0.85rem;
            color: #666;
        }

        .message-meta-item i {
            color: var(--color-gold);
            width: 16px;
        }

        .message-body {
            font-size: 0.95rem;
            color: #444;
            line-height: 1.6;
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
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
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php">
                        <i class="fas fa-building me-2"></i>Propiedades
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/property-form.php">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Propiedad
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/messages.php" class="active">
                        <i class="fas fa-envelope me-2"></i>Mensajes
                        <?php if ($unread_count > 0): ?>
                            <span class="badge bg-danger ms-2"><?php echo $unread_count; ?></span>
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
                <div class="admin-header d-none d-md-block mb-4 bg-white p-3 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0 fw-bold h4">Mensajes de Contacto</h2>
                            <p class="text-muted mb-0 small">
                                <?php echo count($messages); ?> mensajes totales
                                <?php if ($unread_count > 0): ?>
                                    - <span class="text-gold fw-bold"><?php echo $unread_count; ?> sin leer</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <button class="btn btn-outline-dark btn-sm px-3" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-2"></i>Actualizar
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">Todos los mensajes</option>
                                    <option value="new">Sin leer</option>
                                    <option value="read">Leídos</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" id="searchMessage" placeholder="Buscar por nombre, email o mensaje...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Mensajes -->
                <div id="messagesList">
                    <?php if (empty($messages)): ?>
                        <div class="card text-center py-5 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-envelope-open fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay mensajes</h5>
                                <p class="text-muted mb-0">Los mensajes del formulario de contacto aparecerán aquí</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-card <?php echo ($message['status'] ?? '') === 'new' ? 'unread' : ''; ?>" data-status="<?php echo $message['status'] ?? 'read'; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1 fw-bold h6">
                                            <?php echo escape_output($message['name']); ?>
                                            <?php if (($message['status'] ?? '') === 'new'): ?>
                                                <span class="badge bg-gold ms-2 small">Nuevo</span>
                                            <?php endif; ?>
                                        </h5>
                                        <div class="d-flex flex-wrap gap-3 mt-1">
                                            <span class="message-meta-item">
                                                <i class="fas fa-envelope"></i>
                                                <a href="mailto:<?php echo escape_output($message['email']); ?>" class="text-decoration-none text-muted">
                                                    <?php echo escape_output($message['email']); ?>
                                                </a>
                                            </span>
                                            <?php if (!empty($message['phone'])): ?>
                                                <span class="message-meta-item">
                                                    <i class="fas fa-phone"></i>
                                                    <a href="tel:<?php echo escape_output($message['phone']); ?>" class="text-decoration-none text-muted">
                                                        <?php echo escape_output($message['phone']); ?>
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                            <span class="message-meta-item">
                                                <i class="fas fa-clock"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item" href="mailto:<?php echo escape_output($message['email']); ?>"><i class="fas fa-reply me-2 text-primary"></i>Responder</a></li>
                                            <?php if (!empty($message['phone'])): ?>
                                                <li><a class="dropdown-item" href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $message['phone']); ?>" target="_blank"><i class="fab fa-whatsapp me-2 text-success"></i>WhatsApp</a></li>
                                            <?php endif; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><button class="dropdown-item text-danger" onclick="deleteMessage('<?php echo $message['id']; ?>')"><i class="fas fa-trash me-2"></i>Eliminar</button></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="message-body">
                                    <?php echo nl2br(escape_output($message['message'])); ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="mailto:<?php echo escape_output($message['email']); ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-reply me-1"></i>Responder
                                    </a>
                                    <?php if (($message['status'] ?? '') === 'new'): ?>
                                        <button onclick="markAsRead('<?php echo $message['id']; ?>')" class="btn btn-sm btn-outline-dark">
                                            Marcar como leído
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

        // Acciones de Mensajes
        function markAsRead(id) {
            // Nota: Aquí se llamaría a una API para actualizar el estado
            alert('En desarrollo: La actualización de estado se implementará próximamente.');
        }

        function deleteMessage(id) {
            if (confirm('¿Está seguro que desea eliminar este mensaje?')) {
                alert('En desarrollo: La eliminación de mensajes se implementará próximamente.');
            }
        }

        // Buscador y Filtros
        const searchInput = document.getElementById('searchMessage');
        const statusSelect = document.getElementById('filterStatus');

        const filterMessages = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const statusFilter = statusSelect.value;
            const messages = document.querySelectorAll('.message-card');

            messages.forEach(card => {
                const text = card.textContent.toLowerCase();
                const status = card.getAttribute('data-status');

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;

                card.style.display = matchesSearch && matchesStatus ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', filterMessages);
        statusSelect.addEventListener('change', filterMessages);
    </script>
</body>

</html>
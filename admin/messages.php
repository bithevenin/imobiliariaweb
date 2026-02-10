<?php
/**
 * Mensajes de Contacto - Ibron Inmobiliaria
 * Visualización y gestión de mensajes recibidos desde el formulario de contacto
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth(); // Requerir autenticación

$page_title = 'Mensajes de Contacto';

// Obtener todos los mensajes desde Supabase
$messages = supabase_get('contact_messages', ['order' => 'created_at.desc']);

if ($messages === false) {
    $messages = [];
    log_error('Failed to fetch messages from Supabase');
}

// Contador de mensajes no leídos
$unread_count = count(array_filter($messages, fn($m) => $m['status'] === 'new'));
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

        .admin-header {
            background: white;
            padding: 15px 0;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }

        .message-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .message-card.unread {
            border-left-color: var(--color-gold);
            background: rgba(212, 167, 69, 0.05);
        }

        .message-card:hover {
            box-shadow: var(--shadow-md);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .message-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .message-meta-item {
            color: #666;
        }

        .message-meta-item i {
            color: var(--color-gold);
            margin-right: 5px;
        }

        .message-body {
            color: #333;
            line-height: 1.6;
        }

        .message-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
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
                            <span class="badge bg-danger ms-2">
                                <?php echo $unread_count; ?>
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
                            <h2 class="mb-0">Mensajes de Contacto</h2>
                            <p class="text-muted mb-0">
                                <?php echo count($messages); ?> mensajes totales
                                <?php if ($unread_count > 0): ?>
                                    - <span class="text-gold"><strong>
                                            <?php echo $unread_count; ?> sin leer
                                        </strong></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Todos los mensajes</option>
                                    <option value="new">Sin leer</option>
                                    <option value="read">Leídos</option>
                                    <option value="archived">Archivados</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="searchMessage"
                                    placeholder="Buscar por nombre, email o mensaje...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Mensajes -->
                <div id="messagesList">
                    <?php if (empty($messages)): ?>
                        <div class="card text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-envelope-open fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay mensajes</h5>
                                <p class="text-muted mb-0">Los mensajes del formulario de contacto aparecerán aquí</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-card <?php echo $message['status'] === 'new' ? 'unread' : ''; ?>"
                                data-message-id="<?php echo $message['id']; ?>">
                                <div class="message-header">
                                    <div>
                                        <h5 class="mb-1">
                                            <?php echo escape_output($message['name']); ?>
                                            <?php if ($message['status'] === 'new'): ?>
                                                <span class="badge bg-gold ms-2">Nuevo</span>
                                            <?php endif; ?>
                                        </h5>
                                        <div class="message-meta">
                                            <span class="message-meta-item">
                                                <i class="fas fa-envelope"></i>
                                                <a href="mailto:<?php echo escape_output($message['email']); ?>">
                                                    <?php echo escape_output($message['email']); ?>
                                                </a>
                                            </span>
                                            <?php if (!empty($message['phone'])): ?>
                                                <span class="message-meta-item">
                                                    <i class="fas fa-phone"></i>
                                                    <a href="tel:<?php echo escape_output($message['phone']); ?>">
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
                                </div>

                                <div class="message-body">
                                    <p class="mb-0">
                                        <?php echo nl2br(escape_output($message['message'])); ?>
                                    </p>
                                </div>

                                <div class="message-actions">
                                    <div class="btn-group btn-group-sm">
                                        <a href="mailto:<?php echo escape_output($message['email']); ?>"
                                            class="btn btn-outline-primary">
                                            <i class="fas fa-reply me-1"></i>Responder
                                        </a>
                                        <?php if (!empty($message['phone'])): ?>
                                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $message['phone']); ?>"
                                                target="_blank" class="btn btn-outline-success">
                                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                            </a>
                                        <?php endif; ?>
                                        <button onclick="markAsRead(<?php echo $message['id']; ?>)"
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-check me-1"></i>Marcar como leído
                                        </button>
                                        <button onclick="deleteMessage(<?php echo $message['id']; ?>)"
                                            class="btn btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Marcar mensaje como leído
        function markAsRead(id) {
            alert('Funcionalidad de marcar como leído pendiente de implementar con Supabase');
            // TODO: Implementar con API de Supabase
        }

        // Eliminar mensaje
        function deleteMessage(id) {
            if (confirm('¿Está seguro que desea eliminar este mensaje?\n\nEsta acción no se puede deshacer.')) {
                alert('Funcionalidad de eliminación pendiente de implementar con Supabase');
                // TODO: Implementar con API de Supabase
            }
        }

        // Filtros de búsqueda
        document.getElementById('searchMessage').addEventListener('input', filterMessages);
        document.getElementById('filterStatus').addEventListener('change', filterMessages);

        function filterMessages() {
            const searchTerm = document.getElementById('searchMessage').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            const messages = document.querySelectorAll('.message-card');

            messages.forEach(message => {
                const text = message.textContent.toLowerCase();
                const isUnread = message.classList.contains('unread');

                let status = 'read';
                if (isUnread) status = 'new';
                if (message.querySelector('.badge') && message.querySelector('.badge').textContent === 'Archivado') status = 'archived';

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;

                message.style.display = matchesSearch && matchesStatus ? '' : 'none';
            });
        }
    </script>

</body>

</html>
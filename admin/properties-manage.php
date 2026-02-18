<?php
/**
 * Gestión de Propiedades - Ibron Inmobiliaria
 * Listado de todas las propiedades con opciones de edición y eliminación
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth(); // Requerir autenticación

$page_title = 'Propiedades';

// Obtener todas las propiedades desde Supabase
$properties = supabase_get('properties', ['order' => 'created_at.desc']);

if ($properties === false) {
    $properties = [];
    log_error('Failed to fetch properties from Supabase');
}

// Contador de mensajes no leídos para el badge
$all_messages = supabase_get('contact_messages', []);
$unread_count = 0;
if ($all_messages) {
    $unread_count = count(array_filter($all_messages, fn($m) => $m['status'] === 'new'));
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
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
                background: rgba(0, 0, 0, 0.5);
                z-index: 998;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        .property-card-admin {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }

        .property-card-admin:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .property-img-small {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .badge-status {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 20px;
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
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo"
                        style="max-width: 120px; height: auto;" class="mb-2">
                    <p class="text-white-50 small mb-0">Admin Panel</p>
                </div>

                <div class="sidebar-menu">
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php" class="active">
                        <i class="fas fa-building me-2"></i>Propiedades
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/property-form.php">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Propiedad
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/messages.php">
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
                            <h2 class="mb-0 fw-bold h4">Gestión de Propiedades</h2>
                            <p class="text-muted mb-0 small">Administra todas las propiedades del sistema</p>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/admin/property-form.php" class="btn btn-primary px-4">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Propiedad
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">Todos los estados</option>
                                    <option value="Disponible">Disponible</option>
                                    <option value="Vendida">Vendida</option>
                                    <option value="Reservada">Reservada</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select form-select-sm" id="filterType">
                                    <option value="">Todos los tipos</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Apartamento">Apartamento</option>
                                    <option value="Villa">Villa</option>
                                    <option value="Solar">Solar</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" id="searchProperty"
                                        placeholder="Buscar por título o ubicación...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla/Lista de Propiedades -->
                <div class="bg-white rounded shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th class="ps-4">Imagen</th>
                                    <th>Detalles</th>
                                    <th class="d-none d-md-table-cell">Precio</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="propertyList">
                                <?php if (empty($properties)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">No hay propiedades registradas</p>
                                            <a href="<?php echo SITE_URL; ?>/admin/property-form.php"
                                                class="btn btn-primary btn-sm">
                                                Agregar Primera Propiedad
                                            </a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($properties as $property): ?>
                                        <tr data-type="<?php echo $property['type']; ?>"
                                            data-status="<?php echo $property['status']; ?>">
                                            <td class="ps-4">
                                                <img src="<?php echo get_property_image($property['image_main'] ?? ''); ?>"
                                                    alt="" class="property-img-small shadow-sm">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo escape_output($property['title']); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i
                                                        class="fas fa-map-marker-alt me-1"></i><?php echo escape_output($property['location']); ?>
                                                    <span class="mx-1">|</span>
                                                    <?php
                                                    $curr = 'DOP';
                                                    if (!empty($property['features'])) {
                                                        $feats = is_array($property['features']) ? $property['features'] : pg_array_to_php_array($property['features']);
                                                        if (in_array('USD', $feats))
                                                            $curr = 'USD';
                                                    }
                                                    ?>
                                                    <span
                                                        class="d-md-none fw-bold text-gold"><?php echo format_price($property['price'], $curr); ?></span>
                                                    <span
                                                        class="d-none d-md-inline"><?php echo escape_output($property['type']); ?></span>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell fw-bold text-gold">
                                                <?php echo format_price($property['price'], $curr); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = match ($property['status']) {
                                                    'Disponible' => 'bg-success',
                                                    'Vendida' => 'bg-danger',
                                                    'Reservada' => 'bg-warning text-dark',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?php echo $status_class; ?> badge-status">
                                                    <?php echo escape_output($property['status']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo SITE_URL; ?>/admin/property-form.php?id=<?php echo $property['id']; ?>"
                                                        class="btn btn-light border" title="Editar">
                                                        <i class="fas fa-edit text-primary"></i>
                                                    </a>
                                                    <button
                                                        onclick="deleteProperty('<?php echo $property['id']; ?>', '<?php echo addslashes(escape_output($property['title'])); ?>')"
                                                        class="btn btn-light border" title="Eliminar">
                                                        <i class="fas fa-trash text-danger"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
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

        // Función para eliminar propiedad
        function deleteProperty(id, title) {
            if (confirm(`¿Está seguro que desea eliminar la propiedad "${title}"?\n\nEsta acción borrará también todas sus imágenes y no se puede deshacer.`)) {

                const btn = event.currentTarget;
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch('<?php echo SITE_URL; ?>/api/delete-property.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = btn.closest('tr');
                            row.style.transition = 'all 0.3s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                row.remove();
                                if (document.querySelectorAll('#propertyList tr').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        } else {
                            alert('Error: ' + (data.error || 'No se pudo eliminar la propiedad'));
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error de conexión al intentar eliminar');
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
            }
        }

        // Filtros mejorados
        const searchInput = document.getElementById('searchProperty');
        const statusSelect = document.getElementById('filterStatus');
        const typeSelect = document.getElementById('filterType');

        const filterTable = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const statusFilter = statusSelect.value;
            const typeFilter = typeSelect.value;
            const rows = document.querySelectorAll('#propertyList tr');

            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) return;

                const text = row.textContent.toLowerCase();
                const status = row.getAttribute('data-status');
                const type = row.getAttribute('data-type');

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesType = !typeFilter || type === typeFilter;

                row.style.display = matchesSearch && matchesStatus && matchesType ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', filterTable);
        statusSelect.addEventListener('change', filterTable);
        typeSelect.addEventListener('change', filterTable);
    </script>
</body>

</html>
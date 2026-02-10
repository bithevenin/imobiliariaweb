<?php
/**
 * Gestión de Propiedades - Ibron Inmobiliaria
 * Listado de todas las propiedades con opciones de edición y eliminación
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth(); // Requerir autenticación

$page_title = 'Gestión de Propiedades';

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

        .property-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .property-img-small {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 5px 10px;
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
                    <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php" class="active">
                        <i class="fas fa-building me-2"></i>Propiedades
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/property-form.php">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Propiedad
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/messages.php">
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
                            <h2 class="mb-0">Gestión de Propiedades</h2>
                            <p class="text-muted mb-0">Administra todas las propiedades del sistema</p>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/admin/property-form.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Propiedad
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Todos los estados</option>
                                    <option value="Disponible">Disponible</option>
                                    <option value="Vendida">Vendida</option>
                                    <option value="Reservada">Reservada</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterType">
                                    <option value="">Todos los tipos</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Apartamento">Apartamento</option>
                                    <option value="Villa">Villa</option>
                                    <option value="Solar">Solar</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="searchProperty"
                                    placeholder="Buscar por título o ubicación...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Propiedades -->
                <div class="property-table">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Imagen</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Ubicación</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($properties)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No hay propiedades registradas</p>
                                            <a href="<?php echo SITE_URL; ?>/admin/property-form.php"
                                                class="btn btn-primary mt-3">
                                                <i class="fas fa-plus-circle me-2"></i>Agregar Primera Propiedad
                                            </a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($properties as $property): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo escape_output($property['image_main'] ?? 'https://via.placeholder.com/80x60'); ?>"
                                                    alt="<?php echo escape_output($property['title']); ?>"
                                                    class="property-img-small">
                                            </td>
                                            <td>
                                                <strong>
                                                    <?php echo escape_output($property['title']); ?>
                                                </strong>
                                                <?php if ($property['featured']): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Destacada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo escape_output($property['type']); ?>
                                            </td>
                                            <td>
                                                <?php echo escape_output($property['location']); ?>
                                            </td>
                                            <td><strong class="text-gold">
                                                    <?php echo format_price($property['price']); ?>
                                                </strong></td>
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
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo SITE_URL; ?>/admin/property-form.php?id=<?php echo $property['id']; ?>"
                                                        class="btn btn-outline-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button
                                                        onclick="deleteProperty(<?php echo $property['id']; ?>, '<?php echo escape_output($property['title']); ?>')"
                                                        class="btn btn-outline-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para eliminar propiedad
        function deleteProperty(id, title) {
            if (confirm(`¿Está seguro que desea eliminar la propiedad "${title}"?\n\nEsta acción no se puede deshacer.`)) {
                alert('Funcionalidad de eliminación pendiente de implementar con Supabase');
                // TODO: Implementar eliminación con API de Supabase
            }
        }

        // Filtros de búsqueda (funcionalidad básica del lado del cliente)
        document.getElementById('searchProperty').addEventListener('input', filterTable);
        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('filterType').addEventListener('change', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchProperty').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            const typeFilter = document.getElementById('filterType').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip empty state row

                const title = row.cells[1].textContent.toLowerCase();
                const type = row.cells[2].textContent;
                const location = row.cells[3].textContent.toLowerCase();
                const status = row.cells[5].textContent.trim();

                const matchesSearch = title.includes(searchTerm) || location.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesType = !typeFilter || type === typeFilter;

                row.style.display = matchesSearch && matchesStatus && matchesType ? '' : 'none';
            });
        }
    </script>

</body>

</html>
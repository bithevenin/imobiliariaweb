<?php
/**
 * Página de Propiedades - Ibron Inmobiliaria
 * Catálogo completo con sistema de filtros
 */

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';
$page_title = 'Propiedades';

// Obtener parámetros de filtro de la URL
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$type_filter = isset($_GET['type']) ? sanitize_input($_GET['type']) : '';
$min_price = isset($_GET['min_price']) ? (float) $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float) $_GET['max_price'] : PHP_FLOAT_MAX;
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';

// Construir filtros para Supabase
$filters = ['order' => 'created_at.desc'];

if ($type_filter) {
    $filters['type'] = 'eq.' . $type_filter;
}

if ($status_filter) {
    $filters['status'] = 'eq.' . $status_filter;
}

// Filtros de precio
if ($min_price > 0) {
    $filters['price'] = 'gte.' . $min_price;
}
if ($max_price < PHP_FLOAT_MAX) {
    $filters['price'] = isset($filters['price'])
        ? $filters['price'] . ',lte.' . $max_price
        : 'lte.' . $max_price;
}

// Obtener todas las propiedades desde Supabase
$all_properties = supabase_get('properties', $filters);

// Si falla la conexión, usar array vacío
if ($all_properties === false) {
    $all_properties = [];
    log_error('Failed to fetch properties from Supabase');
}

// Filtrar por búsqueda de texto (búsqueda simple en título y ubicación)
if ($search && !empty($all_properties)) {
    $all_properties = array_filter($all_properties, function ($property) use ($search) {
        return stripos($property['title'], $search) !== false ||
            stripos($property['location'], $search) !== false;
    });
}

$filtered_properties = $all_properties;
$total_properties = count($filtered_properties);

include_once __DIR__ . '/includes/header.php';
?>

<!-- Header de Página -->
<section class="bg-black text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="mb-3" style="color: white;">Nuestras Propiedades</h1>
                <p class="lead mb-0">
                    Explora nuestra selección de propiedades premium en las mejores ubicaciones
                </p>
            </div>
            <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
                <p class="mb-0">
                    <span class="text-gold font-weight-bold fs-4"><?php echo $total_properties; ?></span>
                    <?php echo $total_properties === 1 ? 'Propiedad encontrada' : 'Propiedades encontradas'; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filtros -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="bg-white p-4 rounded shadow-sm">
            <form method="GET" action="" id="filterForm">
                <div class="row g-3">
                    <!-- Búsqueda -->
                    <div class="col-lg-3 col-md-6">
                        <label for="search" class="form-label">
                            <i class="fas fa-search text-gold me-2"></i>Buscar
                        </label>
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Ubicación o nombre..." value="<?php echo escape_output($search); ?>">
                    </div>

                    <!-- Tipo de Propiedad -->
                    <div class="col-lg-3 col-md-6">
                        <label for="type" class="form-label">
                            <i class="fas fa-home text-gold me-2"></i>Tipo de Propiedad
                        </label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Todos los tipos</option>
                            <option value="Casa" <?php echo $type_filter === 'Casa' ? 'selected' : ''; ?>>Casa</option>
                            <option value="Apartamento" <?php echo $type_filter === 'Apartamento' ? 'selected' : ''; ?>>
                                Apartamento</option>
                            <option value="Villa" <?php echo $type_filter === 'Villa' ? 'selected' : ''; ?>>Villa</option>
                            <option value="Solar" <?php echo $type_filter === 'Solar' ? 'selected' : ''; ?>>Solar</option>
                            <option value="Penthouse" <?php echo $type_filter === 'Penthouse' ? 'selected' : ''; ?>>
                                Penthouse</option>
                            <option value="Local Comercial" <?php echo $type_filter === 'Local Comercial' ? 'selected' : ''; ?>>Local Comercial</option>
                            <option value="Oficina" <?php echo $type_filter === 'Oficina' ? 'selected' : ''; ?>>Oficina
                            </option>
                        </select>
                    </div>

                    <!-- Precio Mínimo -->
                    <div class="col-lg-2 col-md-6">
                        <label for="min_price" class="form-label">
                            <i class="fas fa-dollar-sign text-gold me-2"></i>Precio Min
                        </label>
                        <select class="form-select" id="min_price" name="min_price">
                            <option value="0">Sin mínimo</option>
                            <option value="1000000" <?php echo $min_price == 1000000 ? 'selected' : ''; ?>>RD$ 1M</option>
                            <option value="3000000" <?php echo $min_price == 3000000 ? 'selected' : ''; ?>>RD$ 3M</option>
                            <option value="5000000" <?php echo $min_price == 5000000 ? 'selected' : ''; ?>>RD$ 5M</option>
                            <option value="10000000" <?php echo $min_price == 10000000 ? 'selected' : ''; ?>>RD$ 10M
                            </option>
                            <option value="20000000" <?php echo $min_price == 20000000 ? 'selected' : ''; ?>>RD$ 20M
                            </option>
                        </select>
                    </div>

                    <!-- Precio Máximo -->
                    <div class="col-lg-2 col-md-6">
                        <label for="max_price" class="form-label">
                            <i class="fas fa-dollar-sign text-gold me-2"></i>Precio Max
                        </label>
                        <select class="form-select" id="max_price" name="max_price">
                            <option value="">Sin máximo</option>
                            <option value="5000000" <?php echo $max_price == 5000000 ? 'selected' : ''; ?>>RD$ 5M</option>
                            <option value="10000000" <?php echo $max_price == 10000000 ? 'selected' : ''; ?>>RD$ 10M
                            </option>
                            <option value="20000000" <?php echo $max_price == 20000000 ? 'selected' : ''; ?>>RD$ 20M
                            </option>
                            <option value="30000000" <?php echo $max_price == 30000000 ? 'selected' : ''; ?>>RD$ 30M
                            </option>
                            <option value="50000000" <?php echo $max_price == 50000000 ? 'selected' : ''; ?>>RD$ 50M
                            </option>
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="col-lg-2 col-md-6">
                        <label for="status" class="form-label">
                            <i class="fas fa-tag text-gold me-2"></i>Estado
                        </label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="Disponible" <?php echo $status_filter === 'Disponible' ? 'selected' : ''; ?>>
                                Disponible</option>
                            <option value="Vendida" <?php echo $status_filter === 'Vendida' ? 'selected' : ''; ?>>Vendida
                            </option>
                            <option value="Reservada" <?php echo $status_filter === 'Reservada' ? 'selected' : ''; ?>>
                                Reservada</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Aplicar Filtros
                            </button>
                            <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-outline-gold">
                                <i class="fas fa-redo me-2"></i>Limpiar Filtros
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Resultados -->
<section class="section-padding">
    <div class="container">
        <?php if (empty($filtered_properties)): ?>
            <!-- No hay resultados -->
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x text-gold mb-4"></i>
                <h3>No se encontraron propiedades</h3>
                <p class="text-muted mb-4">
                    Intenta ajustar los filtros para obtener mejores resultados
                </p>
                <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-primary">
                    Ver Todas las Propiedades
                </a>
            </div>
        <?php else: ?>
            <!-- Grid de Propiedades -->
            <div class="row g-4">
                <?php foreach ($filtered_properties as $index => $property): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                        <div class="property-card">
                            <div class="property-card-img">
                                <img src="<?php echo escape_output($property['image_main'] ?? 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&auto=format&fit=crop'); ?>"
                                    alt="<?php echo escape_output($property['title']); ?>">
                                <?php if ($property['status'] === 'Vendida'): ?>
                                    <span class="property-badge sold">Vendida</span>
                                <?php elseif ($property['status'] === 'Reservada'): ?>
                                    <span class="property-badge reserved">Reservada</span>
                                <?php endif; ?>
                            </div>

                            <div class="property-card-body">
                                <div class="property-type"><?php echo escape_output($property['type']); ?></div>
                                <h3 class="property-title"><?php echo escape_output($property['title']); ?></h3>

                                <div class="property-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo escape_output($property['location']); ?>
                                </div>

                                <?php if ($property['bedrooms'] > 0 || $property['bathrooms'] > 0): ?>
                                    <div class="property-features">
                                        <?php if ($property['bedrooms'] > 0): ?>
                                            <div class="property-feature">
                                                <i class="fas fa-bed"></i>
                                                <span><?php echo $property['bedrooms']; ?> Hab.</span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($property['bathrooms'] > 0): ?>
                                            <div class="property-feature">
                                                <i class="fas fa-bath"></i>
                                                <span><?php echo $property['bathrooms']; ?> Baños</span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="property-feature">
                                            <i class="fas fa-ruler-combined"></i>
                                            <span><?php echo format_area($property['area']); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="property-footer">
                                    <div class="property-price">
                                        <?php
                                        $curr = 'DOP';
                                        if (!empty($property['features'])) {
                                            $feats = is_array($property['features']) ? $property['features'] : pg_array_to_php_array($property['features']);
                                            if (in_array('USD', $feats))
                                                $curr = 'USD';
                                        }
                                        echo format_price($property['price'], $curr);
                                        ?>
                                    </div>
                                    <?php if ($property['status'] === 'Disponible'): ?>
                                        <a href="<?php echo SITE_URL; ?>/property-detail.php?id=<?php echo $property['id']; ?>"
                                            class="btn btn-sm btn-outline-gold">
                                            Ver Detalles
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No disponible</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<?php if (!empty($filtered_properties)): ?>
    <section class="section-padding bg-gold">
        <div class="container text-center">
            <h2 class="mb-4 text-black">¿No encontraste lo que buscabas?</h2>
            <p class="lead mb-4 text-black">
                Contáctanos y te ayudaremos a encontrar la propiedad perfecta para ti
            </p>
            <a href="<?php echo SITE_URL; ?>/index.php#contacto" class="btn btn-dark btn-lg">
                <i class="fas fa-phone me-2"></i>Contáctanos Ahora
            </a>
        </div>
    </section>
<?php endif; ?>

<?php
include_once __DIR__ . '/includes/social-buttons.php';
include_once __DIR__ . '/includes/footer.php';
?>

<!-- AOS Animation -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
</script>
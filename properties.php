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
$ciudad_filter = isset($_GET['ciudad']) ? sanitize_input($_GET['ciudad']) : '';
$bedrooms_filter = isset($_GET['bedrooms']) ? (int) $_GET['bedrooms'] : 0;
$bathrooms_filter = isset($_GET['bathrooms']) ? (int) $_GET['bathrooms'] : 0;
$precio_range = isset($_GET['precio']) ? sanitize_input($_GET['precio']) : '';

// Parámetros de precio manuales (compatibilidad)
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float) $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float) $_GET['max_price'] : PHP_FLOAT_MAX;
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';
$currency_filter = isset($_GET['currency']) ? sanitize_input($_GET['currency']) : ''; // Defaults to empty to show all

// Exchange rate
$exchange_rate = defined('CURRENCY_EXCHANGE_RATE') ? CURRENCY_EXCHANGE_RATE : 60.0;

// Procesar rango de precio de la portada (ej: usd-100k-500k)
if ($precio_range) {
    $parts = explode('-', $precio_range);
    if (count($parts) >= 2) {
        $currency_filter = strtoupper($parts[0]);
        $min_str = $parts[1];
        $max_str = $parts[2] ?? '';
        
        // Función para convertir k/m a números
        $to_num = function($str) {
            $str = strtolower($str);
            $val = (float) $str;
            if (strpos($str, 'k') !== false) $val *= 1000;
            if (strpos($str, 'm') !== false) $val *= 1000000;
            return $val;
        };
        
        $min_price = $to_num($min_str);
        if ($max_str && $max_str !== '+') {
            $max_price = $to_num($max_str);
        } else {
            $max_price = PHP_FLOAT_MAX;
        }
    }
}

// Construir filtros para Supabase
$filters = ['order' => 'created_at.desc'];

if ($type_filter) {
    $filters['type'] = 'eq.' . $type_filter;
}

if ($status_filter) {
    $filters['status'] = 'eq.' . $status_filter;
}

if ($ciudad_filter) {
    $filters['ciudad'] = 'eq.' . $ciudad_filter;
}

// Obtener todas las propiedades desde Supabase
$all_properties = supabase_get('properties', $filters);

// Si falla la conexión, usar array vacío
if ($all_properties === false) {
    $all_properties = [];
    log_error('Failed to fetch properties from Supabase');
}

// Filtrar en PHP (para búsquedas complejas o campos no indexados)
$filtered_properties = array_filter($all_properties ?? [], function ($property) use ($search, $bedrooms_filter, $bathrooms_filter, $min_price, $max_price, $currency_filter, $exchange_rate) {
    // 1. Búsqueda por texto (Título, Ubicación, Sector)
    if ($search) {
        $search_found = stripos($property['title'] ?? '', $search) !== false ||
                        stripos($property['location'] ?? '', $search) !== false ||
                        (isset($property['sector']) && stripos($property['sector'], $search) !== false);
        if (!$search_found) return false;
    }
    
    // 2. Filtro de Habitaciones (GTE)
    if ($bedrooms_filter > 0 && (int)($property['bedrooms'] ?? 0) < $bedrooms_filter) {
        return false;
    }
    
    // 3. Filtro de Baños (GTE)
    if ($bathrooms_filter > 0 && (int)($property['bathrooms'] ?? 0) < $bathrooms_filter) {
        return false;
    }
    
    // 4. Filtro de Moneda y Precio Inteligente
    $property_price = (float) ($property['price'] ?? 0);
    $property_currency = 'DOP'; // Default
    if (!empty($property['features'])) {
        $features = is_array($property['features']) 
            ? $property['features'] 
            : pg_array_to_php_array($property['features']);
        
        if (in_array('USD', $features)) {
            $property_currency = 'USD';
        }
    }
    
    // 4a. Filtro de Moneda explícito (si se seleccionó)
    if ($currency_filter && $property_currency !== $currency_filter) {
        return false;
    }
    
    // 4b. Filtro de Precio con conversión cruzada
    // Usamos el currency_filter como la moneda de referencia del usuario. 
    // Si no hay currency_filter, usamos la moneda de la propiedad tal cual.
    $reference_currency = $currency_filter ?: $property_currency;
    
    $comparable_price = $property_price;
    if ($property_currency !== $reference_currency) {
        if ($property_currency === 'USD' && $reference_currency === 'DOP') {
            $comparable_price = $property_price * $exchange_rate;
        } elseif ($property_currency === 'DOP' && $reference_currency === 'USD') {
            $comparable_price = $property_price / $exchange_rate;
        }
    }
    
    if ($comparable_price < $min_price || $comparable_price > $max_price) {
        return false;
    }
    
    return true;
});

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

<!-- Contenido Principal -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar de Filtros -->
            <div class="col-lg-3">
                <div class="bg-white p-4 rounded shadow-sm sticky-sidebar mb-4">
                    <h4 class="mb-4 d-flex align-items-center">
                        <i class="fas fa-sliders-h text-gold me-2"></i>Filtros
                    </h4>
                    
                    <form method="GET" action="" id="filterForm">
                        <div class="row g-3">
                            <!-- Búsqueda -->
                            <div class="col-12">
                                <label for="search" class="form-label">
                                    <i class="fas fa-search text-gold me-2"></i>Buscar
                                </label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Ubicación o nombre..." value="<?php echo escape_output($search); ?>">
                            </div>

                            <!-- Tipo de Propiedad -->
                            <div class="col-12">
                                <label for="type" class="form-label">
                                    <i class="fas fa-home text-gold me-2"></i>Tipo
                                </label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">Todos los tipos</option>
                                    <option value="Casa" <?php echo $type_filter === 'Casa' ? 'selected' : ''; ?>>Casa</option>
                                    <option value="Apartamento" <?php echo $type_filter === 'Apartamento' ? 'selected' : ''; ?>>Apartamento</option>
                                    <option value="Villa" <?php echo $type_filter === 'Villa' ? 'selected' : ''; ?>>Villa</option>
                                    <option value="Solar" <?php echo $type_filter === 'Solar' ? 'selected' : ''; ?>>Solar</option>
                                    <option value="Penthouse" <?php echo $type_filter === 'Penthouse' ? 'selected' : ''; ?>>Penthouse</option>
                                    <option value="Local Comercial" <?php echo $type_filter === 'Local Comercial' ? 'selected' : ''; ?>>Local Comercial</option>
                                    <option value="Oficina" <?php echo $type_filter === 'Oficina' ? 'selected' : ''; ?>>Oficina</option>
                                </select>
                            </div>

                            <!-- Ciudad -->
                            <div class="col-12">
                                <label for="ciudad" class="form-label">
                                    <i class="fas fa-map-marker-alt text-gold me-2"></i>Ciudad/Ubicación
                                </label>
                                <select class="form-select" id="ciudad" name="ciudad">
                                    <option value="">Todas las ubicaciones</option>
                                    <?php
                                    $cities = [
                                        'Azua', 'Bahoruco', 'Baní', 'Barahona', 'Bávaro', 'Bayahibe', 'Boca Chica', 'Cabarete', 
                                        'Casa de Campo', 'Constanza', 'Cotuí', 'Dajabón', 'Distrito Nacional', 'Duarte', 
                                        'Elías Piña', 'El Seibo', 'Espaillat', 'Gaspar Hernández', 'Hato Mayor', 
                                        'Hermanas Mirabal', 'Higüey', 'Independencia', 'Jarabacoa', 'Juan Dolio', 
                                        'La Altagracia', 'La Romana', 'Las Galeras', 'Las Terrenas', 'La Vega', 
                                        'Mao', 'María Trinidad Sánchez', 'Moca', 'Monseñor Nouel', 'Monte Cristi', 
                                        'Monte Plata', 'Nagua', 'Pedernales', 'Peravia', 'Puerto Plata', 'Punta Cana', 
                                        'Río San Juan', 'Samaná', 'San Cristóbal', 'San Francisco de Macorís', 
                                        'San José de Ocoa', 'San Juan', 'San Pedro de Macorís', 'Sánchez Ramírez', 
                                        'Santiago', 'Santiago Rodríguez', 'Santo Domingo Provincia', 'Santo Domingo Este', 
                                        'Santo Domingo Norte', 'Santo Domingo Oeste', 'Sosúa', 'Valverde'
                                    ];
                                    foreach ($cities as $city) {
                                        $selected = ($ciudad_filter === $city) ? 'selected' : '';
                                        echo "<option value='$city' $selected>$city</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Moneda -->
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign text-gold me-2"></i>Moneda
                                </label>
                                <div class="currency-toggle">
                                    <input type="radio" name="currency" id="currency-all" value="" 
                                        <?php echo ($currency_filter === '') ? 'checked' : ''; ?>>
                                    <label for="currency-all" class="currency-option">Todas</label>

                                    <input type="radio" name="currency" id="currency-dop" value="DOP" 
                                        <?php echo ($currency_filter === 'DOP') ? 'checked' : ''; ?>>
                                    <label for="currency-dop" class="currency-option">DOP</label>
                                    
                                    <input type="radio" name="currency" id="currency-usd" value="USD"
                                        <?php echo ($currency_filter === 'USD') ? 'checked' : ''; ?>>
                                    <label for="currency-usd" class="currency-option">USD</label>
                                </div>
                                <small class="text-muted mt-1 d-block">Tasa: 1 USD = <?php echo $exchange_rate; ?> DOP</small>
                            </div>

                            <!-- Rango de Precio -->
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-chart-line text-gold me-2"></i>Precio
                                </label>
                                <div class="price-range-container">
                                    <div class="d-flex justify-content-between mb-2 small">
                                        <span id="price-min-display" class="fw-bold text-gold">$0</span>
                                        <span id="price-max-display" class="fw-bold text-gold">$22M</span>
                                    </div>
                                    <div id="price-range-slider" class="mb-3"></div>
                                    <input type="hidden" name="min_price" id="min_price" value="<?php echo $min_price; ?>">
                                    <input type="hidden" name="max_price" id="max_price" value="<?php echo $max_price > 0 && $max_price < PHP_FLOAT_MAX ? $max_price : ''; ?>">
                                </div>
                            </div>

                            <!-- Habitaciones -->
                            <div class="col-12">
                                <label for="bedrooms" class="form-label">
                                    <i class="fas fa-bed text-gold me-2"></i>Habitaciones
                                </label>
                                <select class="form-select" id="bedrooms" name="bedrooms">
                                    <option value="0">Cualquiera</option>
                                    <option value="1" <?php echo $bedrooms_filter === 1 ? 'selected' : ''; ?>>1+</option>
                                    <option value="2" <?php echo $bedrooms_filter === 2 ? 'selected' : ''; ?>>2+</option>
                                    <option value="3" <?php echo $bedrooms_filter === 3 ? 'selected' : ''; ?>>3+</option>
                                    <option value="4" <?php echo $bedrooms_filter === 4 ? 'selected' : ''; ?>>4+</option>
                                </select>
                            </div>

                            <!-- Estado -->
                            <div class="col-12">
                                <label for="status" class="form-label">
                                    <i class="fas fa-tag text-gold me-2"></i>Estado
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="Disponible" <?php echo $status_filter === 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                                    <option value="Vendida" <?php echo $status_filter === 'Vendida' ? 'selected' : ''; ?>>Vendida</option>
                                    <option value="Reservada" <?php echo $status_filter === 'Reservada' ? 'selected' : ''; ?>>Reservada</option>
                                </select>
                            </div>

                            <div class="col-12 pt-3">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-filter me-2"></i>Filtrar
                                </button>
                                <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-outline-gold w-100">
                                    <i class="fas fa-redo me-2"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listado de Propiedades -->
            <div class="col-lg-9">
                <?php if (empty($filtered_properties)): ?>
                    <div class="text-center py-5 bg-white rounded shadow-sm">
                        <i class="fas fa-search fa-4x text-gold mb-4"></i>
                        <h3>No se encontraron propiedades</h3>
                        <p class="text-muted mb-4">Intenta ajustar los filtros para obtener mejores resultados</p>
                        <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-primary">Ver Todas</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($filtered_properties as $index => $property): ?>
                            <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                                <div class="property-card h-100">
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
                                        <div class="property-location"><i class="fas fa-map-marker-alt"></i> <?php echo escape_output($property['location']); ?></div>
                                        <div class="property-features">
                                            <?php if ($property['bedrooms'] > 0): ?>
                                                <div class="property-feature"><i class="fas fa-bed"></i> <span><?php echo $property['bedrooms']; ?></span></div>
                                            <?php endif; ?>
                                            <?php if ($property['bathrooms'] > 0): ?>
                                                <div class="property-feature"><i class="fas fa-bath"></i> <span><?php echo $property['bathrooms']; ?></span></div>
                                            <?php endif; ?>
                                            <div class="property-feature"><i class="fas fa-ruler-combined"></i> <span><?php echo format_area($property['area']); ?></span></div>
                                        </div>
                                        <div class="property-footer">
                                            <div class="property-price">
                                                <?php
                                                $curr = 'DOP';
                                                if (!empty($property['features'])) {
                                                    $feats = is_array($property['features']) ? $property['features'] : pg_array_to_php_array($property['features']);
                                                    if (in_array('USD', $feats)) $curr = 'USD';
                                                }
                                                echo format_price($property['price'], $curr);
                                                ?>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-gold share-btn" 
                                                    data-id="<?php echo $property['id']; ?>"
                                                    data-title="<?php echo escape_output($property['title']); ?>"
                                                    data-url="property-detail.php?id=<?php echo $property['id']; ?>"
                                                    title="Compartir">
                                                    <i class="fas fa-share-alt"></i>
                                                </button>
                                                <a href="<?php echo SITE_URL; ?>/property-detail.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-outline-gold">Detalles</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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

<!-- noUiSlider CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">

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

<!-- noUiSlider JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<!-- Price Range Slider Script -->
<script>
    // Currency ranges configuration
    const priceRanges = {
        'USD': {
            min: 0,
            max: 22000000,
            step: 100000,
            symbol: '$',
            prefix: 'US$'
        },
        'DOP': {
            min: 0,
            max: 50000000,
            step: 500000,
            symbol: 'RD$',
            prefix: 'RD$'
        }
    };

    // Get current currency from radio buttons
    function getCurrentCurrency() {
        const currencyRadio = document.querySelector('input[name="currency"]:checked');
        return (currencyRadio && currencyRadio.value) ? currencyRadio.value : 'DOP'; // Fallback to DOP if "Todas" is selected for UI limits
    }

    // Format price for display
    function formatPrice(value, currency) {
        const config = priceRanges[currency];
        if (value === 0) return config.prefix + ' 0';
        
        // Convert to millions for better readability
        if (value >= 1000000) {
            const millions = value / 1000000;
            return config.prefix + ' ' + (millions % 1 === 0 ? millions : millions.toFixed(1)) + 'M';
        }
        
        // Convert to thousands
        if (value >= 1000) {
            const thousands = value / 1000;
            return config.prefix + ' ' + (thousands % 1 === 0 ? thousands : thousands.toFixed(0)) + 'K';
        }
        
        return config.prefix + ' ' + value.toLocaleString();
    }

    // Initialize slider
    let priceSlider;
    const sliderElement = document.getElementById('price-range-slider');
    
    function initializeSlider(currency) {
        const config = priceRanges[currency];
        
        // Get current values from GET parameters or use defaults
        const urlParams = new URLSearchParams(window.location.search);
        const currentMin = parseInt(urlParams.get('min_price')) || config.min;
        const currentMax = parseInt(urlParams.get('max_price')) || config.max;
        
        // Destroy existing slider if it exists
        if (priceSlider) {
            priceSlider.destroy();
        }
        
        // Create slider
        noUiSlider.create(sliderElement, {
            start: [currentMin, currentMax],
            connect: true,
            step: config.step,
            range: {
                'min': config.min,
                'max': config.max
            },
            format: {
                to: value => Math.round(value),
                from: value => Math.round(value)
            }
        });
        
        priceSlider = sliderElement.noUiSlider;
        
        // Update displays when slider changes
        priceSlider.on('update', function(values) {
            const min = parseInt(values[0]);
            const max = parseInt(values[1]);
            
            document.getElementById('price-min-display').textContent = formatPrice(min, currency);
            document.getElementById('price-max-display').textContent = formatPrice(max, currency);
            
            document.getElementById('min_price').value = min;
            document.getElementById('max_price').value = max === config.max ? '' : max;
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentCurrency = getCurrentCurrency();
        initializeSlider(currentCurrency);
        
        // Handle currency change
        const currencyRadios = document.querySelectorAll('input[name="currency"]');
        currencyRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                initializeSlider(this.value);
            });
        });
        
        // Auto-submit on slider change (optional - can be removed if prefer manual submit)
        // priceSlider.on('change', function() {
        //     document.getElementById('filterForm').submit();
        // });
    });
</script>

<style>
    /* Currency Toggle Styles */
    .currency-toggle {
        display: flex;
        gap: 0;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 4px;
        position: relative;
    }
    
    .currency-toggle input[type="radio"] {
        display: none;
    }
    
    .currency-toggle .currency-option {
        flex: 1;
        padding: 8px 16px;
        text-align: center;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #6c757d;
        margin: 0;
    }
    
    .currency-toggle input[type="radio"]:checked + .currency-option {
        background: linear-gradient(135deg, #d4af37 0%, #f4e5a1 100%);
        color: #000;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
    }
    
    .currency-toggle .currency-option:hover {
        color: #000;
    }
    
    /* noUiSlider Custom Styles */
    #price-range-slider {
        height: 8px;
        margin: 20px 0;
    }
    
    .noUi-target {
        background: #e9ecef;
        border-radius: 8px;
        border: none;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .noUi-connect {
        background: linear-gradient(90deg, #d4af37 0%, #f4e5a1 100%);
        box-shadow: 0 2px 4px rgba(212, 175, 55, 0.2);
    }
    
    .noUi-handle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid #d4af37;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .noUi-handle:before,
    .noUi-handle:after {
        display: none;
    }
    
    .noUi-handle:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
    }
    
    .noUi-handle:active {
        transform: scale(1.05);
    }
    
    .noUi-tooltip {
        display: none;
    }
    
    /* Sidebar Styles */
    .sticky-sidebar {
        position: sticky;
        top: 100px;
        z-index: 10;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .price-range-container {
        padding: 5px 10px;
    }
    
    .text-gold {
        color: #d4af37 !important;
    }
    
    /* Price Display Styles */
    .price-display {
        min-width: 120px;
    }
    
    .price-display .fw-bold {
        font-size: 1.1rem;
        color: #d4af37;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .currency-toggle .currency-option {
            padding: 6px 12px;
            font-size: 0.9rem;
        }
        
        #price-range-slider {
            margin: 15px 10px;
        }
    }
</style>
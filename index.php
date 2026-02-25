<?php
/**
 * Página de Inicio - Ibron Inmobiliaria
 * Incluye hero, propiedades destacadas, servicios y formulario de contacto
 */

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';

// Configurar título de página
$page_title = 'Inicio';

// Obtener propiedades destacadas desde Supabase
$featured_properties = supabase_get('properties', [
    'featured' => 'eq.true',
    'status' => 'eq.Disponible',
    'order' => 'created_at.desc',
    'limit' => '6'
]);

// Si falla la conexión, usar array vacío
if ($featured_properties === false) {
    $featured_properties = [];
    log_error('Failed to fetch featured properties from Supabase');
}

// Incluir header
include_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section with Advanced Search -->
<section class="hero hero-with-search">
    <div class="hero-content">
        <!-- Search form focus -->
        <p class="hero-description" data-aos="fade-up">
            Encuentra la propiedad de tus sueños. Ofrecemos las mejores opciones en
            casas, apartamentos, villas y solares en República Dominicana.
        </p>

        <!-- Advanced Search Form -->
        <div class="search-form-container" data-aos="fade-up" data-aos-delay="300">
            <form method="GET" action="<?php echo SITE_URL; ?>/properties.php" class="property-search-form">
                <div class="search-grid">
                    <!-- Tipo de Propiedad -->
                    <div class="search-field">
                        <label for="search-type" class="search-label">
                            <i class="fas fa-home"></i> Tipo de Propiedad
                        </label>
                        <select class="form-select search-select" id="search-type" name="type">
                            <option value="">Seleccionar</option>
                            <option value="Casa">Casa</option>
                            <option value="Apartamento">Apartamento</option>
                            <option value="Villa">Villa</option>
                            <option value="Solar">Solar</option>
                            <option value="Penthouse">Penthouse</option>
                            <option value="Local Comercial">Local Comercial</option>
                            <option value="Oficina">Oficina</option>
                        </select>
                    </div>

                    <!-- Ciudad -->
                    <div class="search-field">
                        <label for="search-ciudad" class="search-label">
                            <i class="fas fa-map-marker-alt"></i> Ciudad
                        </label>
                        <select class="form-select search-select" id="search-ciudad" name="ciudad">
                            <option value="">Seleccionar</option>
                            <option value="Azua">Azua</option>
                            <option value="Bahoruco">Bahoruco</option>
                            <option value="Barahona">Barahona</option>
                            <option value="Bávaro">Bávaro</option>
                            <option value="Dajabón">Dajabón</option>
                            <option value="Distrito Nacional">Distrito Nacional</option>
                            <option value="Duarte">Duarte</option>
                            <option value="Elías Piña">Elías Piña</option>
                            <option value="El Seibo">El Seibo</option>
                            <option value="Espaillat">Espaillat</option>
                            <option value="Hato Mayor">Hato Mayor</option>
                            <option value="Hermanas Mirabal">Hermanas Mirabal</option>
                            <option value="Higüey">Higüey</option>
                            <option value="Independencia">Independencia</option>
                            <option value="La Altagracia">La Altagracia</option>
                            <option value="La Romana">La Romana</option>
                            <option value="La Vega">La Vega</option>
                            <option value="María Trinidad Sánchez">María Trinidad Sánchez</option>
                            <option value="Monseñor Nouel">Monseñor Nouel</option>
                            <option value="Monte Cristi">Monte Cristi</option>
                            <option value="Monte Plata">Monte Plata</option>
                            <option value="Pedernales">Pedernales</option>
                            <option value="Peravia">Peravia</option>
                            <option value="Puerto Plata">Puerto Plata</option>
                            <option value="Punta Cana">Punta Cana</option>
                            <option value="Samaná">Samaná</option>
                            <option value="San Cristóbal">San Cristóbal</option>
                            <option value="San José de Ocoa">San José de Ocoa</option>
                            <option value="San Juan">San Juan</option>
                            <option value="San Pedro de Macorís">San Pedro de Macorís</option>
                            <option value="Sánchez Ramírez">Sánchez Ramírez</option>
                            <option value="Santiago">Santiago</option>
                            <option value="Santiago Rodríguez">Santiago Rodríguez</option>
                            <option value="Santo Domingo">Santo Domingo</option>
                            <option value="Valverde">Valverde</option>
                        </select>
                    </div>

                    <!-- Sector -->
                    <div class="search-field">
                        <label for="search-sector" class="search-label">
                            <i class="fas fa-location-dot"></i> Sector
                        </label>
                        <input type="text" class="form-control search-input" id="search-sector" name="search"
                            placeholder="Ej: Piantini, Naco...">
                    </div>

                    <!-- Habitaciones -->
                    <div class="search-field">
                        <label for="search-bedrooms" class="search-label">
                            <i class="fas fa-bed"></i> Habitaciones
                        </label>
                        <select class="form-select search-select" id="search-bedrooms" name="bedrooms">
                            <option value="">Seleccionar</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                            <option value="5">5+</option>
                        </select>
                    </div>

                    <!-- Baños -->
                    <div class="search-field">
                        <label for="search-bathrooms" class="search-label">
                            <i class="fas fa-bath"></i> Baños
                        </label>
                        <select class="form-select search-select" id="search-bathrooms" name="bathrooms">
                            <option value="">Seleccionar</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                        </select>
                    </div>

                    <!-- Precio -->
                    <div class="search-field">
                        <label for="search-price" class="search-label">
                            <i class="fas fa-dollar-sign"></i> Precio
                        </label>
                        <select class="form-select search-select" id="search-price" name="precio">
                            <option value="">Seleccionar</option>
                            <optgroup label="Pesos Dominicanos">
                                <option value="dop-0-5m">RD$0 - RD$5M</option>
                                <option value="dop-5m-10m">RD$5M - RD$10M</option>
                                <option value="dop-10m-20m">RD$10M - RD$20M</option>
                                <option value="dop-20m-50m">RD$20M - RD$50M</option>
                                <option value="dop-50m+">RD$50M+</option>
                            </optgroup>
                            <optgroup label="Dólares">
                                <option value="usd-0-100k">US$0 - US$100K</option>
                                <option value="usd-100k-500k">US$100K - US$500K</option>
                                <option value="usd-500k-1m">US$500K - US$1M</option>
                                <option value="usd-1m-5m">US$1M - US$5M</option>
                                <option value="usd-5m+">US$5M+</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <!-- Search Button -->
                <div class="search-button-wrapper">
                    <button type="submit" class="btn btn-primary btn-lg search-btn">
                        <i class="fas fa-search me-2"></i>Buscar Propiedades
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Servicios Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Nuestros Servicios</h2>
            <p>Soluciones inmobiliarias completas para tu inversión</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <div class="mb-3">
                        <i class="fas fa-home fa-3x text-gold"></i>
                    </div>
                    <h4>Ventas</h4>
                    <p class="text-muted">
                        Amplio catálogo de propiedades en venta. Casas, apartamentos,
                        villas y más opciones para tu inversión.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <div class="mb-3">
                        <i class="fas fa-key fa-3x text-gold"></i>
                    </div>
                    <h4>Alquiler</h4>
                    <p class="text-muted">
                        Propiedades en alquiler ideales para ti. Encuentra el lugar
                        perfecto para vivir o establecer tu negocio.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <div class="mb-3">
                        <i class="fas fa-handshake fa-3x text-gold"></i>
                    </div>
                    <h4>Asesoría Inmobiliaria</h4>
                    <p class="text-muted">
                        Expertos en bienes raíces a tu servicio. Te guiamos en cada
                        paso de tu proceso de compra o venta.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Propiedades Destacadas -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Propiedades Destacadas</h2>
            <p>Descubre nuestras mejores opciones seleccionadas especialmente para ti</p>
        </div>

        <div class="row g-4">
            <?php foreach ($featured_properties as $index => $property): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                    <div class="property-card">
                        <div class="property-card-img">
                            <img src="<?php echo escape_output($property['image_main'] ?? 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&auto=format&fit=crop'); ?>"
                                alt="<?php echo escape_output($property['title']); ?>">
                            <?php if ($property['status'] === 'Disponible'): ?>
                                <span class="property-badge">Destacada</span>
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
                                <a href="<?php echo SITE_URL; ?>/property-detail.php?id=<?php echo $property['id']; ?>"
                                    class="btn btn-sm btn-outline-gold">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-primary btn-lg">
                Ver Todas las Propiedades <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Estadísticas -->
<section class="section-padding bg-black text-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stat-item">
                    <h2 class="text-gold mb-2" style="font-size: 3rem; font-weight: 800;">500+</h2>
                    <p class="mb-0" style="font-size: 1.1rem;">Propiedades Vendidas</p>
                </div>
            </div>

            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <h2 class="text-gold mb-2" style="font-size: 3rem; font-weight: 800;">1200+</h2>
                    <p class="mb-0" style="font-size: 1.1rem;">Clientes Satisfechos</p>
                </div>
            </div>

            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <h2 class="text-gold mb-2" style="font-size: 3rem; font-weight: 800;">15+</h2>
                    <p class="mb-0" style="font-size: 1.1rem;">Años de Experiencia</p>
                </div>
            </div>

            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <h2 class="text-gold mb-2" style="font-size: 3rem; font-weight: 800;">100%</h2>
                    <p class="mb-0" style="font-size: 1.1rem;">Compromiso</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tips para Comprar tu Primera Casa -->
<section class="section-padding" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <div class="section-title">
            <h2>Tips para Comprar tu Primera Casa</h2>
            <p>Te acompañamos en cada paso de tu inversión inmobiliaria</p>
        </div>

        <div class="row g-4">
            <!-- Tip 1 -->
            <div class="col-lg-6" data-aos="fade-up">
                <div class="tip-card" onclick="toggleTip(1)">
                    <div class="tip-header">
                        <div class="tip-number">1</div>
                        <div class="tip-title-wrapper">
                            <h4 class="tip-title mb-0">Elabora un Presupuesto</h4>
                            <i class="fas fa-chevron-down tip-icon"></i>
                        </div>
                    </div>
                    <div class="tip-content" id="tip-1">
                        <p class="mb-3">
                            Define claramente cuánto puedes destinar a la compra de tu propiedad.
                            Considera no solo el precio de compra, sino también:
                        </p>
                        <ul class="tip-list">
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Gastos de cierre y notaría</li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Impuestos y transferencias</li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Posibles remodelaciones</li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Presupuesto para emergencias</li>
                        </ul>
                        <div class="tip-cta">
                            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn btn-sm btn-outline-gold">
                                <i class="fas fa-phone me-2"></i>Solicita Asesoría
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tip 2 -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="tip-card" onclick="toggleTip(2)">
                    <div class="tip-header">
                        <div class="tip-number" style="background: #2c3e50;">2</div>
                        <div class="tip-title-wrapper">
                            <h4 class="tip-title mb-0">Contrata un Profesional</h4>
                            <i class="fas fa-chevron-down tip-icon"></i>
                        </div>
                    </div>
                    <div class="tip-content" id="tip-2">
                        <p class="mb-3">
                            Los servicios de un profesional que conozca el mercado son invaluables.
                            En <?php echo SITE_NAME; ?> te ayudamos a:
                        </p>
                        <ul class="tip-list">
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Identificar las mejores oportunidades
                            </li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Negociar el mejor precio</li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Verificar la documentación legal</li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Acompañarte en todo el proceso</li>
                        </ul>
                        <div class="tip-cta">
                            <a href="<?php echo SITE_URL; ?>/about.php" class="btn btn-sm btn-outline-gold">
                                <i class="fas fa-users me-2"></i>Conoce a Nuestro Equipo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tip 3 -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="tip-card" onclick="toggleTip(3)">
                    <div class="tip-header">
                        <div class="tip-number" style="background: #8b6f47;">3</div>
                        <div class="tip-title-wrapper">
                            <h4 class="tip-title mb-0">Visita Diferentes Opciones</h4>
                            <i class="fas fa-chevron-down tip-icon"></i>
                        </div>
                    </div>
                    <div class="tip-content" id="tip-3">
                        <p class="mb-3">
                            No te quedes con la primera opción. Visita varias propiedades para
                            hacer comparaciones y tomar la mejor decisión:
                        </p>
                        <ul class="tip-list">
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Compara ubicaciones y vecindarios</li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Evalúa el estado de la propiedad</li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Analiza la relación calidad-precio
                            </li>
                            <li><i class="fas fa-check-circle text-gold me-2"></i>Considera el potencial de valorización
                            </li>
                        </ul>
                        <div class="tip-cta">
                            <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-sm btn-outline-gold">
                                <i class="fas fa-search me-2"></i>Ver Propiedades
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tip 4: CTA Contacto -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
                <div class="tip-card tip-card-cta">
                    <div class="text-center p-4">
                        <div class="mb-4">
                            <i class="fas fa-headset fa-4x text-gold"></i>
                        </div>
                        <h4 class="mb-3">¿Listo para Comprar tu Primera Casa?</h4>
                        <p class="mb-4">
                            Nuestro equipo de expertos está listo para asesorarte en cada paso
                            del proceso de compra de tu primera propiedad.
                        </p>
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn btn-primary">
                                <i class="fas fa-phone me-2"></i><?php echo CONTACT_PHONE_FORMATTED; ?>
                            </a>
                            <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode('Hola! Me gustaría información sobre comprar mi primera casa'); ?>"
                                target="_blank" class="btn btn-success">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .tip-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }

    .tip-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-5px);
    }

    .tip-card-cta {
        background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);
        color: white;
        cursor: default;
    }

    .tip-card-cta:hover {
        transform: none;
    }

    .tip-card-cta h4,
    .tip-card-cta p {
        color: white;
    }

    .tip-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 15px;
    }

    .tip-number {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: var(--color-gold);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .tip-title-wrapper {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tip-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--color-black);
    }

    .tip-icon {
        font-size: 1.2rem;
        color: var(--color-gold);
        transition: transform 0.3s ease;
    }

    .tip-card.active .tip-icon {
        transform: rotate(180deg);
    }

    .tip-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, opacity 0.3s ease, padding-top 0.3s ease;
        opacity: 0;
        padding-top: 0;
    }

    .tip-content.active {
        max-height: 500px;
        opacity: 1;
        padding-top: 20px;
        border-top: 2px solid var(--color-gold);
    }

    .tip-list {
        list-style: none;
        padding: 0;
        margin-bottom: 20px;
    }

    .tip-list li {
        padding: 8px 0;
        display: flex;
        align-items: start;
    }

    .tip-cta {
        text-align: center;
        padding-top: 15px;
    }

    @media (max-width: 768px) {
        .tip-number {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }

        .tip-title {
            font-size: 1.1rem;
        }
    }
</style>

<script>
    function toggleTip(tipNumber) {
        const tipContent = document.getElementById(`tip-${tipNumber}`);
        const tipCard = tipContent.closest('.tip-card');

        // Toggle active class
        if (tipContent.classList.contains('active')) {
            tipContent.classList.remove('active');
            tipCard.classList.remove('active');
        } else {
            // Close all other tips
            document.querySelectorAll('.tip-content').forEach(content => {
                content.classList.remove('active');
                content.closest('.tip-card').classList.remove('active');
            });

            // Open clicked tip
            tipContent.classList.add('active');
            tipCard.classList.add('active');
        }
    }
</script>

<?php
// Incluir botones sociales y footer
include_once __DIR__ . '/includes/social-buttons.php';
include_once __DIR__ . '/includes/footer.php';
?>

<!-- AOS Animation Library -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Manejo del formulario de contacto
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const messageDiv = document.getElementById('formMessage');
            const submitBtn = this.querySelector('button[type="submit"]');

            // Deshabilitar botón durante envío
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';

            // Enviar a API
            fetch('<?php echo SITE_URL; ?>/api/contact.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || '¡Mensaje enviado exitosamente!', 'success');
                        document.getElementById('contactForm').reset();
                    } else {
                        showToast(data.message || 'Error al enviar el mensaje', 'error');
                    }

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Mensaje';
                })
                .catch(error => {
                    showToast('Error de conexión. Por favor intenta de nuevo.', 'error');

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Mensaje';
                });
        });
    }
</script>
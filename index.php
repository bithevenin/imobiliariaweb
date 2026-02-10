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

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title" data-aos="fade-up">
            <?php echo SITE_NAME; ?>
        </h1>
        <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
            <?php echo SITE_TAGLINE; ?>
        </p>
        <p class="hero-description" data-aos="fade-up" data-aos-delay="200">
            Encuentra la propiedad de tus sueños. Ofrecemos las mejores opciones en 
            casas, apartamentos, villas y solares en República Dominicana.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap" data-aos="fade-up" data-aos-delay="300">
            <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search me-2"></i>Ver Propiedades
            </a>
            <a href="#contacto" class="btn btn-outline-gold btn-lg">
                <i class="fas fa-phone me-2"></i>Contáctanos
            </a>
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
                                <?php echo format_price($property['price']); ?>
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

<!-- Formulario de Contacto -->
<section id="contacto" class="section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Contáctanos</h2>
            <p>¿Tienes dudas o estás interesado en alguna propiedad? Escríbenos</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded shadow">
                    <form id="contactForm" method="POST" action="<?php echo SITE_URL; ?>/api/contact.php">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre Completo *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       required 
                                       placeholder="Tu nombre">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo Electrónico *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required 
                                       placeholder="tu@email.com">
                            </div>
                            
                            <div class="col-12">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="809-555-1234">
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label">Mensaje *</label>
                                <textarea class="form-control" 
                                          id="message" 
                                          name="message" 
                                          rows="5" 
                                          required 
                                          placeholder="Cuéntanos en qué podemos ayudarte..."></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div id="formMessage" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Incluir footer y botones sociales
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
    document.getElementById('contactForm').addEventListener('submit', function(e) {
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
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = data.message;
                document.getElementById('contactForm').reset();
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = data.message;
            }
            messageDiv.style.display = 'block';
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Mensaje';
            
            // Ocultar mensaje después de 5 segundos
            setTimeout(function() {
                messageDiv.style.display = 'none';
            }, 5000);
        })
        .catch(error => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error de conexión. Por favor intenta de nuevo.';
            messageDiv.style.display = 'block';
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Mensaje';
        });
    });
</script>

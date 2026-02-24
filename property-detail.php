<?php
/**
 * Página de Detalle de Propiedad - Ibron Inmobiliaria
 * Muestra información completa de una propiedad específica
 */

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';

// Obtener ID de la propiedad
$property_id = $_GET['id'] ?? null;

if (!$property_id) {
    header('Location: ' . SITE_URL . '/properties.php');
    exit;
}

// Obtener datos de la propiedad
$properties = supabase_get('properties', ['id' => 'eq.' . $property_id]);

if (!$properties || count($properties) === 0) {
    header('Location: ' . SITE_URL . '/properties.php');
    exit;
}

$property = $properties[0];

// Extraer moneda de features
$currency = 'DOP';
if (!empty($property['features'])) {
    $features_arr = is_array($property['features']) ? $property['features'] : pg_array_to_php_array($property['features']);
    if (in_array('USD', $features_arr)) {
        $currency = 'USD';
    }
}

// Preparar galería de imágenes
$gallery_images = [];
if (!empty($property['image_main'])) {
    $gallery_images[] = $property['image_main'];
}
if (!empty($property['image_gallery'])) {
    $gallery_arr = is_array($property['image_gallery']) ? $property['image_gallery'] : pg_array_to_php_array($property['image_gallery']);
    foreach ($gallery_arr as $img) {
        if (!empty(trim($img))) {
            $gallery_images[] = trim($img);
        }
    }
}

$page_title = $property['title'];
include_once __DIR__ . '/includes/header.php';
?>

<!-- Property Detail Section -->
<section class="py-5 bg-light">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/properties.php">Propiedades</a></li>
                <li class="breadcrumb-item active">
                    <?php echo escape_output($property['title']); ?>
                </li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Galería de Imágenes -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <?php if (!empty($gallery_images)): ?>
                            <!-- Imagen Principal -->
                            <div class="main-image-container mb-3 position-relative">
                                <img src="<?php echo escape_output($gallery_images[0]); ?>"
                                    alt="<?php echo escape_output($property['title']); ?>"
                                    class="img-fluid w-100 rounded-top" id="mainImage"
                                    style="max-height: 500px; object-fit: cover; cursor: zoom-in;" onclick="openLightbox()">

                                <?php if (count($gallery_images) > 1): ?>
                                    <!-- Controles de Navegación -->
                                    <button
                                        class="btn btn-dark btn-sm position-absolute top-50 start-0 translate-middle-y ms-3 opacity-75"
                                        id="prevBtn" style="z-index: 10; border-radius: 50%; width: 40px; height: 40px;"
                                        onclick="navigateImage(-1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button
                                        class="btn btn-dark btn-sm position-absolute top-50 end-0 translate-middle-y me-3 opacity-75"
                                        id="nextBtn" style="z-index: 10; border-radius: 50%; width: 40px; height: 40px;"
                                        onclick="navigateImage(1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>

                                    <!-- Indicador de Imagen Actual -->
                                    <div class="position-absolute bottom-0 end-0 mb-3 me-3 bg-dark bg-opacity-75 text-white px-3 py-1 rounded"
                                        style="z-index: 10; font-size: 0.875rem;">
                                        <span id="currentImageIndex">1</span> /
                                        <?php echo count($gallery_images); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Miniaturas -->
                            <?php if (count($gallery_images) > 1): ?>
                                <div class="px-3 pb-3">
                                    <div class="row g-2" id="thumbnails">
                                        <?php foreach ($gallery_images as $index => $image): ?>
                                            <div class="col-3 col-md-2">
                                                <img src="<?php echo escape_output($image); ?>"
                                                    alt="Imagen <?php echo $index + 1; ?>"
                                                    class="img-fluid rounded thumbnail-image <?php echo $index === 0 ? 'active' : ''; ?>"
                                                    style="cursor: pointer; height: 80px; object-fit: cover; width: 100%; border: 2px solid transparent;"
                                                    onclick="changeMainImage('<?php echo escape_output($image); ?>', this)">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <img src="<?php echo get_property_image($property); ?>"
                                alt="<?php echo escape_output($property['title']); ?>" class="img-fluid w-100 rounded-top"
                                style="max-height: 500px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3 fw-bold">Descripción</h3>
                        <p class="text-muted">
                            <?php echo nl2br(escape_output($property['description'])); ?>
                        </p>
                    </div>
                </div>

                <!-- Características y Amenidades -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <?php
                        $features = !empty($property['features']) ? (is_array($property['features']) ? $property['features'] : pg_array_to_php_array($property['features'])) : [];
                        $amenities = !empty($property['amenities']) ? (is_array($property['amenities']) ? $property['amenities'] : pg_array_to_php_array($property['amenities'])) : [];

                        // Filtrar monedas de las características
                        $features = array_filter($features, fn($f) => !in_array($f, ['DOP', 'USD']));
                        ?>

                        <?php if (!empty($features)): ?>
                            <h3 class="h5 mb-3 fw-bold">Características</h3>
                            <ul class="list-unstyled row">
                                <?php foreach ($features as $feature): ?>
                                    <li class="col-md-6 mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <?php echo escape_output($feature); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($amenities)): ?>
                            <h3 class="h5 mb-3 fw-bold mt-4">Amenidades</h3>
                            <ul class="list-unstyled row">
                                <?php foreach ($amenities as $amenity): ?>
                                    <li class="col-md-6 mb-2">
                                        <i class="fas fa-star text-gold me-2"></i>
                                        <?php echo escape_output($amenity); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar con Información -->
            <div class="col-lg-4">
                <!-- Precio y Detalles Principales -->
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="h3 text-gold fw-bold mb-0">
                                <?php echo format_price($property['price'], $currency); ?>
                            </h2>
                            <span
                                class="badge bg-<?php echo $property['status'] === 'Disponible' ? 'success' : ($property['status'] === 'Vendida' ? 'danger' : 'warning'); ?> mt-2">
                                <?php echo escape_output($property['status']); ?>
                            </span>
                        </div>

                        <hr>

                        <div class="property-details">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-home me-2"></i>Tipo
                                </span>
                                <span class="fw-bold">
                                    <?php echo escape_output($property['type']); ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-bed me-2"></i>Habitaciones
                                </span>
                                <span class="fw-bold">
                                    <?php echo $property['bedrooms']; ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-bath me-2"></i>Baños
                                </span>
                                <span class="fw-bold">
                                    <?php echo $property['bathrooms']; ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-ruler-combined me-2"></i>Área
                                </span>
                                <span class="fw-bold">
                                    <?php echo format_area($property['area']); ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i>Ubicación
                                </span>
                                <span class="fw-bold">
                                    <?php echo escape_output($property['location']); ?>
                                </span>
                            </div>
                        </div>

                        <hr>

                        <!-- Botones de Contacto -->
                        <div class="d-grid gap-2">
                            <a href="#contacto" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Contactar
                            </a>
                            <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', CONTACT_PHONE); ?>?text=Hola, estoy interesado en: <?php echo urlencode($property['title']); ?>"
                                target="_blank" class="btn btn-success">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn btn-outline-dark">
                                <i class="fas fa-phone me-2"></i>Llamar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulario de Contacto -->
<section id="contacto" class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">¿Interesado en esta propiedad?</h2>
                    <p class="text-muted">Déjanos tus datos y te contactaremos pronto</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="contactForm" class="needs-validation" novalidate>
                            <input type="hidden" name="property_id" value="<?php echo escape_output($property_id); ?>">
                            <input type="hidden" name="property_title"
                                value="<?php echo escape_output($property['title']); ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono *</label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Asunto</label>
                                    <input type="text" class="form-control" name="subject"
                                        value="Consulta sobre: <?php echo escape_output($property['title']); ?>">
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border mb-3">
                                        <p class="mb-1 small text-muted text-uppercase fw-bold">Propiedad de interés:</p>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($gallery_images)): ?>
                                                <img src="<?php echo escape_output($gallery_images[0]); ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?php echo escape_output($property['title']); ?></h6>
                                                <p class="mb-0 small text-gold fw-bold"><?php echo format_price($property['price'], $currency); ?> • <?php echo escape_output($property['type']); ?></p>
                                                <p class="mb-0 x-small text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo escape_output($property['location']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <label class="form-label">Tus comentarios o preguntas *</label>
                                    <textarea class="form-control" name="comments" rows="4" placeholder="Escribe aquí tus dudas sobre esta propiedad..." required></textarea>

                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen p-0 m-0">
        <div class="modal-content border-0 bg-dark bg-opacity-95">
            <div class="modal-header border-0 p-3">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center p-0 position-relative">
                <!-- Botones de Navegación del Lightbox -->
                <button
                    class="btn btn-link text-white position-absolute start-0 top-50 translate-middle-y ms-2 ms-md-4 lightbox-nav-btn"
                    onclick="navigateImage(-1)">
                    <i class="fas fa-chevron-left fa-2x"></i>
                </button>
                <button
                    class="btn btn-link text-white position-absolute end-0 top-50 translate-middle-y me-2 ms-md-4 lightbox-nav-btn"
                    onclick="navigateImage(1)">
                    <i class="fas fa-chevron-right fa-2x"></i>
                </button>

                <img src="" id="lightboxImage" class="img-fluid" style="max-height: 90vh; object-fit: contain;">

                <div
                    class="position-absolute bottom-0 start-50 translate-middle-x mb-4 bg-dark bg-opacity-75 text-white px-4 py-2 rounded-pill small">
                    <span id="lightboxCounter">1 / 1</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .thumbnail-image.active {
        border-color: var(--color-gold) !important;
    }

    .thumbnail-image:hover {
        opacity: 0.8;
    }

    .sticky-top {
        position: sticky;
    }
    /* Lightbox Styles */
    #lightboxModal .modal-content {
        background: rgba(0, 0, 0, 0.9) !important;
        backdrop-filter: blur(5px);
    }

    .lightbox-nav-btn {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        text-decoration: none;
        z-index: 1060;
    }

    .lightbox-nav-btn:hover {
        background: rgba(212, 167, 69, 0.8);
        color: white !important;
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .lightbox-nav-btn {
            width: 45px;
            height: 45px;
        }

        .lightbox-nav-btn i {
            font-size: 1.2rem;
        }
    }

</style>

<script>
    // Array de imágenes para la galería
    const galleryImages = <?php echo json_encode($gallery_images); ?>;
    let currentImageIndex = 0;
    let lightboxModal;

    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('lightboxModal');
        if (modalEl) {
            lightboxModal = new bootstrap.Modal(modalEl);
        }
    });

    // Abrir Lightbox
    function openLightbox() {
        if (!lightboxModal) return;
        updateLightboxContent();
        lightboxModal.show();
    }

    // Actualizar contenido del Lightbox
    function updateLightboxContent() {
        const lightboxImg = document.getElementById('lightboxImage');
        const lightboxCounter = document.getElementById('lightboxCounter');

        if (lightboxImg && galleryImages[currentImageIndex]) {
            lightboxImg.src = galleryImages[currentImageIndex];
        }

        if (lightboxCounter) {
            lightboxCounter.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
        }
    }


    // Cambiar imagen principal
    function changeMainImage(imageSrc, thumbnail) {
        document.getElementById('mainImage').src = imageSrc;

        // Actualizar índice actual
        currentImageIndex = galleryImages.indexOf(imageSrc);
        updateImageCounter();

        // Actualizar clase active en miniaturas
        document.querySelectorAll('.thumbnail-image').forEach(img => {
            img.classList.remove('active');
        });
        if (thumbnail) {
            thumbnail.classList.add('active');
        }
    }

    // Navegar entre imágenes con flechas
    function navigateImage(direction) {
        currentImageIndex += direction;

        // Loop circular
        if (currentImageIndex < 0) {
            currentImageIndex = galleryImages.length - 1;
        } else if (currentImageIndex >= galleryImages.length) {
            currentImageIndex = 0;
        }

        const newImageSrc = galleryImages[currentImageIndex];
        document.getElementById('mainImage').src = newImageSrc;

        // Si el lightbox está abierto, actualizarlo también
        const lightboxImg = document.getElementById('lightboxImage');
        if (lightboxImg) {
            updateLightboxContent();
        }

        // Actualizar miniatura activa
        const thumbnails = document.querySelectorAll('.thumbnail-image');
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active', index === currentImageIndex);
        });

        updateImageCounter();
    }

    // Actualizar contador de imágenes
    function updateImageCounter() {
        const counter = document.getElementById('currentImageIndex');
        if (counter) {
            counter.textContent = currentImageIndex + 1;
        }
    }

    // Soporte para teclado (flechas izquierda/derecha)
    document.addEventListener('keydown', function (e) {
        if (galleryImages.length > 1) {
            if (e.key === 'ArrowLeft') {
                navigateImage(-1);
            } else if (e.key === 'ArrowRight') {
                navigateImage(1);
            }
        }
    });

    // Manejo del formulario de contacto
    document.getElementById('contactForm').addEventListener('submit', function (e) {
        e.preventDefault();

        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        // El campo 'comments' será procesado por la API para incluir detalles de la propiedad
        data.message = data.comments; 


        fetch('<?php echo SITE_URL; ?>/api/contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.', 'success');
                    this.reset();
                    this.classList.remove('was-validated');
                } else {
                    showToast(data.message || 'Error al enviar el mensaje. Por favor, intenta de nuevo.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al enviar el mensaje. Por favor, intenta de nuevo.', 'error');
            });
    });
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
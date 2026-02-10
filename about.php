<?php
/**
 * Página Acerca De - Ibron Inmobiliaria
 * Historia, misión y equipo
 */

require_once __DIR__ . '/config/settings.php';
$page_title = 'Acerca De';
include_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" style="min-height: 400px;">
    <div class="hero-content">
        <h1 class="hero-title" data-aos="fade-up">Nuestra Historia</h1>
        <p class="hero-description" data-aos="fade-up" data-aos-delay="100">
            Más de 15 años de experiencia en el mercado inmobiliario dominicano
        </p>
    </div>
</section>

<!-- Historia de la Empresa -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&auto=format&fit=crop"
                    alt="Historia Ibron Inmobiliaria" class="img-fluid rounded shadow-lg">
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="mb-4">Historia de
                    <?php echo SITE_NAME; ?>
                </h2>
                <p class="lead text-gold mb-3">
                    Norvi Rosario – Presidente de IBRON Inmobiliaria SRL
                </p>
                <p class="mb-3">
                    IBRON Inmobiliaria SRL es una empresa dedicada a la promoción, comercialización
                    y venta de propiedades, comprometida con ofrecer un servicio confiable,
                    transparente y orientado a satisfacer las necesidades de cada cliente.
                </p>
                <p class="mb-3">
                    Bajo la dirección de su presidente, Norvi Rosario, la empresa trabaja con
                    altos estándares de responsabilidad, asesorando a sus clientes en la compra,
                    venta e inversión de bienes raíces, brindando acompañamiento personalizado en
                    cada proceso para garantizar seguridad y confianza en cada negociación.
                </p>
                <p class="mb-4">
                    Nuestro compromiso es hacer de cada inversión una historia de éxito, trabajando
                    con dedicación para que nuestros clientes logren sus objetivos inmobiliarios.
                </p>

                <div class="d-flex gap-4 flex-wrap">
                    <div>
                        <h4 class="text-gold mb-2">500+</h4>
                        <p class="text-muted mb-0">Propiedades Vendidas</p>
                    </div>
                    <div>
                        <h4 class="text-gold mb-2">1200+</h4>
                        <p class="text-muted mb-0">Clientes Felices</p>
                    </div>
                    <div>
                        <h4 class="text-gold mb-2">15+</h4>
                        <p class="text-muted mb-0">Años de Experiencia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Valores y Misión -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Nuestros Valores</h2>
            <p>Los pilares que nos definen y nos impulsan a la excelencia</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <i class="fas fa-award fa-3x text-gold"></i>
                    </div>
                    <h4>Compromiso</h4>
                    <p class="text-muted mb-0">
                        Nos comprometemos con cada cliente a brindar el mejor servicio
                        y asesoría personalizada en todo momento.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x text-gold"></i>
                    </div>
                    <h4>Confianza</h4>
                    <p class="text-muted mb-0">
                        Construimos relaciones duraderas basadas en la honestidad,
                        transparencia y profesionalismo.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <i class="fas fa-star fa-3x text-gold"></i>
                    </div>
                    <h4>Excelencia</h4>
                    <p class="text-muted mb-0">
                        Nos esforzamos por superar expectativas en cada proyecto,
                        ofreciendo propiedades de la más alta calidad.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <i class="fas fa-handshake fa-3x text-gold"></i>
                    </div>
                    <h4>Profesionalismo</h4>
                    <p class="text-muted mb-0">
                        Equipo altamente capacitado y con amplia experiencia en el
                        sector inmobiliario.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <i class="fas fa-lightbulb fa-3x text-gold"></i>
                    </div>
                    <h4>Innovación</h4>
                    <p class="text-muted mb-0">
                        Utilizamos las últimas tecnologías y estrategias para
                        ofrecer el mejor servicio.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <i class="fas fa-heart fa-3x text-gold"></i>
                    </div>
                    <h4>Pasión</h4>
                    <p class="text-muted mb-0">
                        Amamos lo que hacemos y eso se refleja en cada propiedad
                        que presentamos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nuestro Director -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Nuestro Director</h2>
            <p>Liderazgo con experiencia y visión</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded shadow-lg overflow-hidden" data-aos="fade-up">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-5">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&auto=format&fit=crop"
                                alt="Norvi Rosario - Director" class="img-fluid"
                                style="height: 100%; object-fit: cover;">
                        </div>
                        <div class="col-md-7 p-4 p-md-5">
                            <h3 class="mb-2">Norvi Rosario</h3>
                            <p class="text-gold font-weight-bold mb-3">Presidente de IBRON Inmobiliaria SRL</p>
                            <p class="mb-3">
                                Como presidente de <?php echo SITE_NAME; ?>, Norvi Rosario dirige la empresa
                                con altos estándares de responsabilidad y profesionalismo, comprometido con
                                ofrecer un servicio confiable y transparente a cada cliente.
                            </p>
                            <p class="mb-3">
                                Su visión empresarial se centra en asesorar de manera personalizada a los clientes
                                en la compra, venta e inversión de bienes raíces, garantizando seguridad y confianza
                                en cada negociación.
                            </p>
                            <p class="mb-4">
                                "Trabajamos con dedicación para que nuestros clientes logren sus objetivos. Cada
                                proceso es acompañado personalmente para garantizar el mejor resultado posible."
                            </p>

                            <div class="d-flex gap-3">
                                <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn btn-primary">
                                    <i class="fas fa-phone me-2"></i>Contactar
                                </a>
                                <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="btn btn-outline-gold">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Misión y Visión -->
<section class="section-padding bg-black text-white">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="text-center text-lg-start">
                    <h3 class="text-gold mb-4">
                        <i class="fas fa-bullseye me-2"></i>Nuestra Misión
                    </h3>
                    <p class="lead mb-3">
                        Proporcionar servicios inmobiliarios de excelencia que superen las
                        expectativas de nuestros clientes.
                    </p>
                    <p>
                        Nos dedicamos a facilitar el proceso de compra, venta y alquiler de
                        propiedades mediante un servicio personalizado, profesional y transparente.
                        Nuestro objetivo es construir relaciones duraderas basadas en la confianza
                        y el éxito mutuo.
                    </p>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center text-lg-start">
                    <h3 class="text-gold mb-4">
                        <i class="fas fa-eye me-2"></i>Nuestra Visión
                    </h3>
                    <p class="lead mb-3">
                        Ser la inmobiliaria líder en República Dominicana, reconocida por nuestra
                        excelencia y compromiso.
                    </p>
                    <p>
                        Aspiramos a ser la primera opción para quienes buscan invertir en bienes
                        raíces, ofreciendo un portafolio diverso de propiedades premium y un
                        servicio que establezca nuevos estándares en la industria inmobiliaria
                        dominicana.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Por qué elegirnos -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>¿Por Qué Elegirnos?</h2>
            <p>Razones para confiar en nosotros para tu próxima inversión</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-sm-6" data-aos="zoom-in">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-check fa-2x text-black"></i>
                    </div>
                    <h5>Propiedades Verificadas</h5>
                    <p class="text-muted">Todas nuestras propiedades están legalmente verificadas</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-headset fa-2x text-black"></i>
                    </div>
                    <h5>Asesoría 24/7</h5>
                    <p class="text-muted">Estamos disponibles cuando nos necesites</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-dollar-sign fa-2x text-black"></i>
                    </div>
                    <h5>Mejores Precios</h5>
                    <p class="text-muted">Garantizamos precios competitivos en el mercado</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-file-contract fa-2x text-black"></i>
                    </div>
                    <h5>Trámites Seguros</h5>
                    <p class="text-muted">Acompañamiento legal en todo el proceso</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section-padding bg-gold">
    <div class="container text-center" data-aos="fade-up">
        <h2 class="mb-4 text-black">¿Listo para Encontrar tu Propiedad Ideal?</h2>
        <p class="lead mb-4 text-black">
            Nuestro equipo de expertos está listo para ayudarte a hacer realidad tu inversión
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?php echo SITE_URL; ?>/properties.php" class="btn btn-dark btn-lg">
                <i class="fas fa-search me-2"></i>Ver Propiedades
            </a>
            <a href="<?php echo SITE_URL; ?>/index.php#contacto" class="btn btn-outline-dark btn-lg">
                <i class="fas fa-phone me-2"></i>Contáctanos
            </a>
        </div>
    </div>
</section>

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
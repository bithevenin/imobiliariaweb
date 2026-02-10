<?php
/**
 * Página de Contacto - Ibron Inmobiliaria
 * Formulario de contacto y redes sociales
 */

require_once __DIR__ . '/config/settings.php';

$page_title = 'Contacto';
include_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section"
    style="background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%); padding: 100px 0 80px;">
    <div class="container text-center text-white">
        <h1 class="display-4 font-weight-bold mb-3" data-aos="fade-up" style="color: white;">Contáctanos</h1>
        <p class="lead mb-0" data-aos="fade-up" data-aos-delay="100">
            Estamos aquí para ayudarte a encontrar tu propiedad ideal
        </p>
    </div>
</section>

<!-- Formulario de Contacto -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded shadow" data-aos="fade-up">
                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open-text fa-3x text-gold mb-3"></i>
                        <h2 class="mb-2">Envíanos un Mensaje</h2>
                        <p class="text-muted">Completa el formulario y te contactaremos a la brevedad</p>
                    </div>

                    <form id="contactForm" method="POST" action="<?php echo SITE_URL; ?>/api/contact.php">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user text-gold me-2"></i>Nombre Completo *
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="Tu nombre completo">
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-gold me-2"></i>Correo Electrónico *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="tu@email.com">
                            </div>

                            <div class="col-12">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone text-gold me-2"></i>Teléfono
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="809-555-1234">
                            </div>

                            <div class="col-12">
                                <label for="message" class="form-label">
                                    <i class="fas fa-comment-dots text-gold me-2"></i>Mensaje *
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="6" required
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

<!-- Redes Sociales -->
<section class="section-padding">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Redes Sociales</h2>
            <p>Síguenos y mantente al tanto de nuestras últimas propiedades</p>
        </div>

        <div class="row g-4">
            <!-- Instagram -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up">
                <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" class="social-card instagram">
                    <div class="social-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h4>Instagram</h4>
                    <p class="mb-0">@ibroninmobiliaria</p>
                    <div class="social-overlay">
                        <span>Visitar <i class="fas fa-arrow-right ms-2"></i></span>
                    </div>
                </a>
            </div>

            <!-- Facebook -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" class="social-card facebook">
                    <div class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <h4>Facebook</h4>
                    <p class="mb-0">IBRON Inmobiliaria</p>
                    <div class="social-overlay">
                        <span>Visitar <i class="fas fa-arrow-right ms-2"></i></span>
                    </div>
                </a>
            </div>

            <!-- YouTube -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <a href="<?php echo YOUTUBE_URL; ?>" target="_blank" class="social-card youtube">
                    <div class="social-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h4>YouTube</h4>
                    <p class="mb-0">IBRON Inmobiliaria</p>
                    <div class="social-overlay">
                        <span>Visitar <i class="fas fa-arrow-right ms-2"></i></span>
                    </div>
                </a>
            </div>

            <!-- Email -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="social-card email">
                    <div class="social-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4>Email</h4>
                    <p class="mb-0">
                        <?php echo CONTACT_EMAIL; ?>
                    </p>
                    <div class="social-overlay">
                        <span>Enviar <i class="fas fa-arrow-right ms-2"></i></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Información de Contacto Adicional -->
        <div class="row mt-5">
            <div class="col-lg-4 text-center mb-4" data-aos="fade-up">
                <div class="contact-info-card">
                    <i class="fas fa-phone-alt fa-3x text-gold mb-3"></i>
                    <h5>Llámanos</h5>
                    <a href="tel:<?php echo CONTACT_PHONE; ?>" class="text-decoration-none">
                        <strong>
                            <?php echo CONTACT_PHONE_FORMATTED; ?>
                        </strong>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 text-center mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-info-card">
                    <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                    <h5>WhatsApp</h5>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" class="text-decoration-none">
                        <strong>Chatear Ahora</strong>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 text-center mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-info-card">
                    <i class="fas fa-clock fa-3x text-gold mb-3"></i>
                    <h5>Horario</h5>
                    <strong>Lun - Vie: 9:00 AM - 6:00 PM</strong><br>
                    <span class="text-muted">Sáb: 9:00 AM - 2:00 PM</span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Social Cards Styles */
    .social-card {
        display: block;
        background: white;
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        text-decoration: none;
        color: var(--color-black);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .social-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .social-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.5rem;
        transition: all 0.3s ease;
    }

    .social-card h4 {
        font-weight: 700;
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }

    .social-card p {
        color: #666;
        transition: color 0.3s ease;
    }

    .social-overlay {
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 15px;
        transition: bottom 0.3s ease;
        font-weight: 600;
    }

    .social-card:hover .social-overlay {
        bottom: 0;
    }

    /* Instagram */
    .social-card.instagram .social-icon {
        background: linear-gradient(135deg, #833ab4 0%, #fd1d1d 50%, #fcb045 100%);
        color: white;
    }

    .social-card.instagram:hover {
        background: linear-gradient(135deg, #833ab4 0%, #fd1d1d 50%, #fcb045 100%);
    }

    /* Facebook */
    .social-card.facebook .social-icon {
        background: linear-gradient(135deg, #1877f2 0%, #0c63d4 100%);
        color: white;
    }

    .social-card.facebook:hover {
        background: linear-gradient(135deg, #1877f2 0%, #0c63d4 100%);
    }

    /* YouTube */
    .social-card.youtube .social-icon {
        background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
        color: white;
    }

    .social-card.youtube:hover {
        background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
    }

    /* Email */
    .social-card.email .social-icon {
        background: linear-gradient(135deg, var(--color-gold) 0%, #b8860b 100%);
        color: white;
    }

    .social-card.email:hover {
        background: linear-gradient(135deg, var(--color-gold) 0%, #b8860b 100%);
    }

    .social-card:hover h4,
    .social-card:hover p {
        color: white;
    }

    /* Contact Info Cards */
    .contact-info-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .contact-info-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-5px);
    }

    .contact-info-card a {
        color: var(--color-black);
        transition: color 0.3s ease;
    }

    .contact-info-card a:hover {
        color: var(--color-gold);
    }
</style>

<script>
    // Manejo del formulario de contacto
    document.getElementById('contactForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const formMessage = document.getElementById('formMessage');
        const submitBtn = this.querySelector('button[type="submit"]');

        // Deshabilitar botón
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                formMessage.className = 'alert alert-success';
                formMessage.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + result.message;
                this.reset();
            } else {
                formMessage.className = 'alert alert-danger';
                formMessage.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + result.message;
            }

            formMessage.style.display = 'block';

            // Scroll al mensaje
            formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        } catch (error) {
            formMessage.className = 'alert alert-danger';
            formMessage.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Error al enviar el mensaje. Por favor intenta de nuevo.';
            formMessage.style.display = 'block';
        } finally {
            // Rehabilitar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Mensaje';
        }
    });
</script>

<?php
// Incluir footer y botones sociales
include_once __DIR__ . '/includes/social-buttons.php';
include_once __DIR__ . '/includes/footer.php';
?>
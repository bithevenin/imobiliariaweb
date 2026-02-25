<?php
/**
 * Footer Global - Ibron Inmobiliaria
 * Incluye información de contacto y enlaces
 */

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/settings.php';
}
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <!-- Columna 1: Logo y Descripción -->
            <div class="col-lg-4 col-md-6">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="<?php echo SITE_NAME; ?>"
                    class="footer-logo" style="max-height: 80px; width: auto;">
                <p class="mt-3">
                    <?php echo SITE_NAME; ?> es tu mejor opción en bienes raíces.
                    Ofrecemos propiedades de lujo y excelentes oportunidades de inversión
                    en toda la República Dominicana.
                </p>
                <p class="text-gold font-weight-bold">
                    <?php echo SITE_TAGLINE; ?>
                </p>
            </div>

            <!-- Columna 2: Enlaces Rápidos -->
            <div class="col-lg-2 col-md-6">
                <h5>Enlaces</h5>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/index.php">Inicio</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/properties.php">Propiedades</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/about.php">Acerca De</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </div>

            <!-- Columna 3: Servicios -->
            <div class="col-lg-2 col-md-6">
                <h5>Servicios</h5>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/properties.php?type=Casa">Casas</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/properties.php?type=Apartamento">Apartamentos</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/properties.php?type=Villa">Villas</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/properties.php?type=Solar">Solares</a></li>
                </ul>
            </div>

            <!-- Columna 4: Contacto -->
            <div class="col-lg-4 col-md-6">
                <h5>Contacto</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-phone text-gold me-2"></i>
                        <a href="tel:<?php echo CONTACT_PHONE; ?>">
                            <?php echo CONTACT_PHONE_FORMATTED; ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope text-gold me-2"></i>
                        <a href="mailto:<?php echo CONTACT_EMAIL; ?>">
                            <?php echo CONTACT_EMAIL; ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt text-gold me-2"></i>
                        <?php echo CONTACT_ADDRESS; ?>
                    </li>
                </ul>

                <!-- Redes Sociales -->
                <div class="social-links mt-4">
                    <h6 class="text-gold mb-3">Síguenos</h6>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode(WHATSAPP_MESSAGE); ?>"
                        target="_blank" class="btn btn-outline-gold btn-sm me-2 mb-2">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank"
                        class="btn btn-outline-gold btn-sm me-2 mb-2">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                    <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" class="btn btn-outline-gold btn-sm mb-2">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="mb-0">
                        &copy;
                        <?php echo date('Y'); ?>
                        <?php echo SITE_NAME; ?>. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <?php
                    // Incluir contador de visitas
                    require_once __DIR__ . '/visitor-counter.php';
                    display_visitor_badge(true);
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>

<!-- Scroll to Top on Navbar Scroll Effect -->
<script>
    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999"></div>

<!-- Toast Notification Function -->
<script>
    function showToast(message, type = 'success') {
        const toastContainer = document.querySelector('.toast-container');

        // Determinar el icono y color según el tipo
        let icon, bgClass, textClass;
        switch (type) {
            case 'success':
                icon = 'fa-check-circle';
                bgClass = 'bg-success';
                textClass = 'text-white';
                break;
            case 'error':
            case 'danger':
                icon = 'fa-exclamation-circle';
                bgClass = 'bg-danger';
                textClass = 'text-white';
                break;
            case 'warning':
                icon = 'fa-exclamation-triangle';
                bgClass = 'bg-warning';
                textClass = 'text-dark';
                break;
            case 'info':
                icon = 'fa-info-circle';
                bgClass = 'bg-info';
                textClass = 'text-white';
                break;
            default:
                icon = 'fa-bell';
                bgClass = 'bg-primary';
                textClass = 'text-white';
        }

        // Crear el toast
        const toastId = 'toast-' + Date.now();
        const toastHTML = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} ${textClass} border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas ${icon} me-2 fa-lg"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

        // Agregar al contenedor
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        // Inicializar y mostrar el toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            animation: true,
            autohide: true,
            delay: 5000
        });

        toast.show();

        // Eliminar del DOM después de ocultarse
        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }
</script>

<?php
// Incluir Bot Animado "Norvis" en todas las páginas públicas
include_once __DIR__ . '/bot-avatar.php';
?>

</body>

</html>
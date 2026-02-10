<?php
/**
 * Botones Flotantes de Redes Sociales
 * WhatsApp, Instagram, Facebook
 */

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/settings.php';
}
?>

<!-- Botones Flotantes de Redes Sociales -->
<div class="social-float">
    <!-- WhatsApp -->
    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode(WHATSAPP_MESSAGE); ?>"
        target="_blank" class="social-btn whatsapp" title="Contáctanos por WhatsApp" aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Instagram -->
    <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" class="social-btn instagram" title="Síguenos en Instagram"
        aria-label="Instagram">
        <i class="fab fa-instagram"></i>
    </a>

    <!-- Facebook -->
    <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" class="social-btn facebook" title="Síguenos en Facebook"
        aria-label="Facebook">
        <i class="fab fa-facebook-f"></i>
    </a>

    <!-- YouTube -->
    <a href="<?php echo YOUTUBE_URL; ?>" target="_blank" class="social-btn youtube" title="Suscríbete a YouTube"
        aria-label="YouTube">
        <i class="fab fa-youtube"></i>
    </a>
</div>

<style>
    /* Animación inicial de entrada */
    .social-float {
        animation: slideInRight 0.6s ease-out;
        transition: transform 0.5s ease, opacity 0.5s ease;
    }

    /* Estado oculto */
    .social-float.hidden {
        transform: translateX(100px);
        opacity: 0;
        pointer-events: none;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Efecto pulse en WhatsApp para llamar la atención */
    .social-btn.whatsapp {
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        50% {
            box-shadow: 0 8px 16px rgba(37, 211, 102, 0.5);
        }
    }

    /* Responsive para móviles */
    @media (max-width: 480px) {
        .social-float {
            right: 10px;
            bottom: 10px;
        }

        .social-btn {
            width: 48px !important;
            height: 48px !important;
            font-size: 1.2rem !important;
        }
    }
</style>

<script>
    (function () {
        const socialFloat = document.querySelector('.social-float');
        let hideTimeout;
        let isHidden = false;

        // Función para ocultar los botones
        function hideButtons() {
            if (socialFloat && !isHidden) {
                socialFloat.classList.add('hidden');
                isHidden = true;
            }
        }

        // Función para mostrar los botones
        function showButtons() {
            if (socialFloat) {
                socialFloat.classList.remove('hidden');
                isHidden = false;

                // Limpiar timeout anterior si existe
                clearTimeout(hideTimeout);

                // Ocultar después de 3.5 segundos
                hideTimeout = setTimeout(hideButtons, 3500);
            }
        }

        // Mostrar botones al cargar la página
        window.addEventListener('load', function () {
            // Ocultar después de 3.5 segundos
            hideTimeout = setTimeout(hideButtons, 3500);
        });

        // Mostrar botones cuando el usuario hace clic en cualquier parte
        document.addEventListener('click', function (e) {
            // No reaccionar si se hace clic en los propios botones sociales
            if (!e.target.closest('.social-float')) {
                showButtons();
            }
        });

        // También mostrar al hacer scroll (opcional, pero mejora la UX)
        let scrollTimeout;
        window.addEventListener('scroll', function () {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function () {
                showButtons();
            }, 100);
        });
    })();
</script>
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
</div>

<style>
    /* Asegurar que los botones siempre estén visibles */
    .social-float {
        animation: slideInRight 0.6s ease-out;
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
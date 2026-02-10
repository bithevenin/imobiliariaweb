<?php
/**
 * Header Global - Ibron Inmobiliaria
 * Incluye navegación y branding
 */

// Asegurar que settings.php esté cargado
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/settings.php';
}

// Determinar la página actual para marcar activa en el menú
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?>. Venta y alquiler de propiedades en República Dominicana: casas, apartamentos, villas, solares y más.">
    <meta name="keywords"
        content="inmobiliaria, propiedades, casas, apartamentos, villas, solares, República Dominicana, bienes raíces">
    <meta name="author" content="<?php echo SITE_NAME; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?>">
    <meta property="og:description"
        content="Tu mejor opción en bienes raíces. Propiedades de lujo y excelentes oportunidades de inversión.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/logo.png">

    <title>
        <?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>
        <?php echo SITE_NAME; ?>
    </title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>/index.php">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="<?php echo SITE_NAME; ?>"
                    class="navbar-logo">
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>"
                            href="<?php echo SITE_URL; ?>/index.php">
                            Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'properties' ? 'active' : ''; ?>"
                            href="<?php echo SITE_URL; ?>/properties.php">
                            Propiedades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>"
                            href="<?php echo SITE_URL; ?>/about.php">
                            Acerca De
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">
                            Contacto
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary btn-sm" href="tel:<?php echo CONTACT_PHONE; ?>">
                            <i class="fas fa-phone me-2"></i>
                            <?php echo CONTACT_PHONE_FORMATTED; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Spacer para fixed navbar -->
    <div style="height: 80px;"></div>
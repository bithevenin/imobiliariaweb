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

// Rastrear visita general al sitio (si no es una página de propiedad que ya lo hace)
if ($current_page !== 'property-detail' && function_exists('track_property_view')) {
    // Requiere analytics.php (se incluye vía settings o manual)
    if (!defined('ANALYTICS_LOADED')) {
        require_once __DIR__ . '/../config/analytics.php';
        define('ANALYTICS_LOADED', true);
    }
    track_property_view(null);
}
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
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=3.0">
    <link rel="shortcut icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=3.0">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=3.0">

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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=1.1">
    
    <!-- Translator Styles -->
    <style>
        .nav-item-translator {
            display: flex;
            align-items: center;
            margin-left: 12px;
            position: relative;
            overflow: visible !important;
        }
        .navbar, .navbar-collapse, .navbar-nav, .container {
            overflow: visible !important;
        }
        .language-btn {
            width: auto;
            min-width: 100px;
            height: 38px;
            border-radius: 5px;
            background: #000;
            color: #d4a745;
            border: 2px solid #d4a745;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.25s ease;
            padding: 0 14px;
            outline: none;
            line-height: 1;
        }
        .language-btn:hover { background: #d4a745; color: #000; }
        .language-btn i { font-size: 1rem; display: flex; align-items: center; margin-top: -1px; }
        .language-btn .lang-text { font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center;}
        .language-dropdown {
            position: fixed;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.25);
            width: 210px;
            max-height: 0;
            opacity: 0;
            visibility: hidden;
            transition: max-height 0.3s ease, opacity 0.2s ease;
            overflow: hidden;
            border: 1px solid rgba(212,167,69,0.4);
            z-index: 9999999;
        }
        .language-dropdown.active {
            opacity: 1;
            visibility: visible;
            max-height: 400px;
        }
        .language-dropdown-header {
            background: #111;
            color: #d4a745;
            padding: 9px 14px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .language-options-scroll { max-height: 330px; overflow-y: auto; }
        .language-option {
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: background 0.2s;
            color: #222;
            font-weight: 600;
            font-size: 0.88rem;
            text-decoration: none;
        }
        .language-option:hover { background: rgba(212,167,69,0.1); color: #b8923a; }
        .language-option img { width: 20px; border-radius: 2px; }
        /* === OCULTAR BARRA DE GOOGLE TRANSLATE === */
        /* Ocultar el iframe de la barra superior */
        .goog-te-banner-frame,
        .goog-te-balloon-frame,
        #goog-gt-tt,
        .goog-te-spinner-pos,
        .goog-tooltip,
        .goog-tooltip:hover { 
            display: none !important; 
            visibility: hidden !important;
            opacity: 0 !important;
        }
        /* Evitar que Google empuje el body hacia abajo */
        body { 
            top: 0 !important; 
            position: relative !important;
        }
        /* Forzar que el iframe de traduccion no ocupe espacio ni se vea en absoluto */
        iframe.goog-te-banner-frame { 
            display: none !important; 
            height: 0px !important;
            position: absolute !important;
            top: -9999px !important;
            left: -9999px !important;
            z-index: -1 !important;
        }
        /* Ocultar cualquier div contenedor que GT inyecte al principio del body */
        body > div.skiptranslate {
            display: none !important;
            height: 0px !important;
        }
    </style>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>/index.php">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                <span class="navbar-logo-text">IBRON INMOBILIARIA SRL</span>
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
                        <a class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>"
                            href="<?php echo SITE_URL; ?>/contact.php">
                            Contacto
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary btn-sm" href="tel:<?php echo CONTACT_PHONE; ?>">
                            <i class="fas fa-phone me-2"></i>
                            <?php echo CONTACT_PHONE_FORMATTED; ?>
                        </a>
                    </li>
                    <!-- Translator in Menu -->
                    <li class="nav-item-translator">
                        <button class="language-btn" id="languageBtn" title="Traducir Página">
                            <i class="fas fa-language"></i>
                            <span class="lang-text" id="langText">Traducir Pág</span>
                        </button>
                        <div class="language-dropdown" id="languageDropdown">
                            <div class="language-dropdown-header">Idioma / Language</div>
                            <div class="language-options-scroll">
                                <a class="language-option" href="#" onclick="translatePage('es','ES'); return false;">
                                    <img src="https://flagcdn.com/w20/es.png" alt="Spain"> Espa&ntilde;ol
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('en','EN'); return false;">
                                    <img src="https://flagcdn.com/w20/us.png" alt="USA"> English
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('fr','FR'); return false;">
                                    <img src="https://flagcdn.com/w20/fr.png" alt="France"> Fran&ccedil;ais
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('pt','PT'); return false;">
                                    <img src="https://flagcdn.com/w20/br.png" alt="Brazil"> Portugu&ecirc;s
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('it','IT'); return false;">
                                    <img src="https://flagcdn.com/w20/it.png" alt="Italy"> Italiano
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('de','DE'); return false;">
                                    <img src="https://flagcdn.com/w20/de.png" alt="Germany"> Deutsch
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('ru','RU'); return false;">
                                    <img src="https://flagcdn.com/w20/ru.png" alt="Russia"> &#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('zh-CN','ZH'); return false;">
                                    <img src="https://flagcdn.com/w20/cn.png" alt="China"> &#20013;&#25991;
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('ja','JA'); return false;">
                                    <img src="https://flagcdn.com/w20/jp.png" alt="Japan"> &#26085;&#26412;&#35486;
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('ar','AR'); return false;">
                                    <img src="https://flagcdn.com/w20/sa.png" alt="Arabic"> &#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577;
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('hi','HI'); return false;">
                                    <img src="https://flagcdn.com/w20/in.png" alt="India"> &#2361;&#2367;&#2344;&#2381;&#2342;&#2368;
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('nl','NL'); return false;">
                                    <img src="https://flagcdn.com/w20/nl.png" alt="Netherlands"> Nederlands
                                </a>
                                <a class="language-option" href="#" onclick="translatePage('tr','TR'); return false;">
                                    <img src="https://flagcdn.com/w20/tr.png" alt="Turkey"> T&uuml;rk&ccedil;e
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
    // ============================================================
    // TRADUCTOR – Cookie-Based Google Translate (funciona en localhost)
    // ============================================================
    function translatePage(langCode, shortCode) {
        localStorage.setItem('selectedLanguage', langCode);
        
        // Si quieres que el botón siempre diga "Traducir Pág" puedes comentar estas líneas,
        // pero las mantenemos para que el botón muestre qué idioma está activo si así se desea
        // localStorage.setItem('selectedLanguageShort', shortCode);
        // document.getElementById('langText').textContent = shortCode;
        
        document.getElementById('languageDropdown').classList.remove('active');

        if (langCode === 'es') {
            // Eliminar cookie de traducción y recargar para volver al español
            eraseCookie('googtrans');
        } else {
            // Establecer cookie de Google Translate y recargar
            setCookie('googtrans', '/es/' + langCode);
        }
        location.reload();
    }

    function setCookie(name, value) {
        document.cookie = name + '=' + value + '; path=/';
        document.cookie = name + '=' + value + '; path=/; domain=' + location.hostname;
    }

    function eraseCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + location.hostname;
    }

    // Toggle del dropdown
    document.getElementById('languageBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('languageDropdown');
        const rect = this.getBoundingClientRect();
        dropdown.style.top  = (rect.bottom + 8) + 'px';
        dropdown.style.left = (rect.right - 210) + 'px';
        dropdown.classList.toggle('active');
    });

    document.addEventListener('click', function() {
        const d = document.getElementById('languageDropdown');
        if (d) d.classList.remove('active');
    });

    // Restaurar el botón al cargar
    (function(){
        // Ya no cambiamos el texto por defecto para mantener "Traducir Pág"
        // const saved = localStorage.getItem('selectedLanguageShort');
        // if (saved) document.getElementById('langText').textContent = saved;
        
        const savedLang = localStorage.getItem('selectedLanguage');
        if (savedLang && window.dispatchEvent) {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang: savedLang } }));
            }, 500);
        }
    })();
    </script>

    <!-- Google Translate Widget -->
    <div id="google_translate_element" style="display:none;"></div>
    <script>
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'es',
            autoDisplay: false
        }, 'google_translate_element');
    }

    // Ocultar solo la barra de toolbar (sin interferir con la traduccion del contenido)
    // Usamos setInterval simple en lugar de MutationObserver para no interrumpir GT
    function hideBannerOnly() {
        // Solo apuntar al iframe de la barra, no al contenido
        var bar = document.querySelector('iframe.goog-te-banner-frame');
        if (bar) {
            bar.style.cssText = 'display:none!important;height:0!important;';
        }
        // Corregir posicion del body si GT la movio
        if (document.body) {
            document.body.style.top = '0px';
        }
    }

    // Ejecutar varias veces en los primeros segundos y luego periódicamente
    setTimeout(hideBannerOnly, 300);
    setTimeout(hideBannerOnly, 800);
    setTimeout(hideBannerOnly, 1500);
    setTimeout(hideBannerOnly, 3000);
    setInterval(hideBannerOnly, 5000);
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Spacer para fixed navbar -->
    <div style="height: 80px;"></div>
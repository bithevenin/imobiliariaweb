<?php
/**
 * Test de Conexión a Supabase
 * Verificar que las credenciales funcionen correctamente
 */

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Supabase - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-container {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 167, 69, 0.3);
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .test-title {
            color: #D4A745;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }
        .test-result {
            background: rgba(0,0,0,0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .success {
            border-left: 4px solid #28a745;
        }
        .error {
            border-left: 4px solid #dc3545;
        }
        .info {
            border-left: 4px solid #D4A745;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .credential-label {
            color: #D4A745;
            font-weight: 600;
        }
        .credential-value {
            color: #aaa;
            font-family: monospace;
            font-size: 0.85rem;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .btn-back {
            background: #D4A745;
            color: #000;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #E5C468;
            color: #000;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1 class="test-title">
            <i class="fas fa-database me-2"></i>
            Test de Conexión Supabase
        </h1>

        <!-- Credenciales Configuradas -->
        <div class="test-result info">
            <h5><i class="fas fa-key me-2"></i>Credenciales Configuradas</h5>
            <div class="credential-item">
                <span class="credential-label">URL:</span>
                <span class="credential-value"><?php echo SUPABASE_URL; ?></span>
            </div>
            <div class="credential-item">
                <span class="credential-label">ANON Key:</span>
                <span class="credential-value"><?php echo substr(SUPABASE_ANON_KEY, 0, 30) . '...'; ?></span>
            </div>
        </div>

        <!-- Test de Conexión -->
        <div class="test-result <?php
            $connection_test = test_supabase_connection();
            echo $connection_test ? 'success' : 'error';
        ?>">
            <h5>
                <i class="fas fa-<?php echo $connection_test ? 'check-circle' : 'times-circle'; ?> me-2"></i>
                Estado de Conexión
            </h5>
            <?php if ($connection_test): ?>
                <p class="mb-0">
                    <i class="fas fa-check text-success me-2"></i>
                    <strong>¡Conexión exitosa!</strong> Las credenciales de Supabase son correctas y el servidor está respondiendo.
                </p>
            <?php else: ?>
                <p class="mb-0">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <strong>Error de conexión.</strong> Verifica que:
                    <ul class="mt-2">
                        <li>Las credenciales sean correctas</li>
                        <li>Tengas conexión a internet</li>
                        <li>cURL esté habilitado en PHP</li>
                    </ul>
                </p>
            <?php endif; ?>
        </div>

        <!-- Información de PHP/cURL -->
        <div class="test-result info">
            <h5><i class="fas fa-server me-2"></i>Configuración del Servidor</h5>
            <div class="credential-item">
                <span class="credential-label">PHP Version:</span>
                <span class="credential-value"><?php echo phpversion(); ?></span>
            </div>
            <div class="credential-item">
                <span class="credential-label">cURL:</span>
                <span class="credential-value">
                    <?php echo function_exists('curl_version') ? '✅ Habilitado' : '❌ Deshabilitado'; ?>
                </span>
            </div>
            <?php if (function_exists('curl_version')): 
                $curl_info = curl_version();
            ?>
            <div class="credential-item">
                <span class="credential-label">cURL Version:</span>
                <span class="credential-value"><?php echo $curl_info['version']; ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Botón de regreso -->
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/index.php" class="btn-back">
                <i class="fas fa-home me-2"></i>
                Volver al Sitio
            </a>
        </div>
    </div>
</body>
</html>

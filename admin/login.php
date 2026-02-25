<?php
/**
 * Admin Login - Ibron Inmobiliaria
 * Página de autenticación para administradores
 */

require_once __DIR__ . '/../config/settings.php';

// Si ya está autenticado, redirigir al dashboard
if (is_authenticated()) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit();
}

$error = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_error('POST request recibida en login.php', ['post' => $_POST]);
    
    // Validar CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido';
        log_error('CSRF Validation FAILED', [
            'sent_token' => $_POST['csrf_token'] ?? 'null',
            'session_token' => $_SESSION['csrf_token'] ?? 'null'
        ]);
    } else {
        $username = sanitize_input($_POST['username']);
        $password = $_POST['password']; // No sanitizar password

        // Buscar usuario en Supabase
        $users = supabase_get('users', ['username' => 'eq.' . $username]);
        
        if ($users && count($users) > 0) {
            $user = $users[0];
            
            // Log para depuración
            log_error('Intento de login', [
                'username' => $username,
                'user_found' => true,
                'role' => $user['role'],
                'hash_in_db' => $user['password_hash']
            ]);
            
            // Verificar contraseña
            if (password_verify($password, $user['password_hash'])) {
                // Login exitoso
                log_error('Login exitoso para: ' . $username);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_time'] = time();

                // 1. Guardar sesión en Supabase (user_sessions)
                $token = bin2hex(random_bytes(32));
                $session_data = [
                    'user_id' => $user['id'],
                    'session_token' => $token,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
                ];
                supabase_insert('user_sessions', $session_data);

                // 2. Actualizar último login
                supabase_update('users', $user['id'], ['last_login' => date('Y-m-d H:i:s')]);

                // Regenerar session ID por seguridad
                session_regenerate_id(true);

                // Redirigir al dashboard
                header('Location: ' . SITE_URL . '/admin/dashboard.php');
                exit();
            } else {
                $error = 'Usuario o contraseña incorrectos';
                log_error('Login fallido - contraseña NO coincide', [
                    'username' => $username,
                    'hash_attempted' => password_hash($password, PASSWORD_DEFAULT) // Solo por referencia
                ]);
            }
        } else {
            $error = 'Usuario o contraseña incorrectos';
            log_error('Login fallido - usuario NO encontrado en Supabase', [
                'username' => $username,
                'supabase_response' => $users
            ]);
        }
    }
}

$page_title = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title; ?> -
        <?php echo SITE_NAME; ?>
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=3.0">
    <link rel="shortcut icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=3.0">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.png?v=3.0">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">

    <style>
        body {
            background: linear-gradient(135deg, #2a2a2a 0%, #3d3d3d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header h4 {
            color: white !important;
        }

        .login-header img {
            max-width: 180px;
            height: auto;
            margin-bottom: 20px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-control:focus {
            border-color: var(--color-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 167, 69, 0.25);
        }

        .back-to-site {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-site a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-to-site a:hover {
            color: var(--color-gold);
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="<?php echo SITE_NAME; ?>">
                <h4 class="mb-0">Panel de Administración</h4>
            </div>

            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo escape_output($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                    <div class="mb-4">
                        <label for="username" class="form-label">
                            <i class="fas fa-user text-gold me-2"></i>Usuario
                        </label>
                        <input type="text" class="form-control form-control-lg" id="username" name="username" required
                            autofocus placeholder="Ingresa tu usuario">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock text-gold me-2"></i>Contraseña
                        </label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password"
                            required placeholder="Ingresa tu contraseña">
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                    </div>

                    <div class="text-center text-muted">
                        <small>
                            <i class="fas fa-shield-alt me-1"></i>
                            Acceso seguro con SSL
                        </small>
                    </div>
                </form>
            </div>
        </div>

        <div class="back-to-site">
            <a href="<?php echo SITE_URL; ?>/index.php">
                <i class="fas fa-arrow-left me-2"></i>Volver al sitio web
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
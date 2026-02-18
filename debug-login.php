<?php
/**
 * Debug Login - Verificar hash de contraseña
 */

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';

echo "<h2>Debug Login</h2>";

// Buscar usuario admin
$users = supabase_get('users', ['username' => 'eq.admin']);

if ($users && count($users) > 0) {
    $user = $users[0];

    echo "<h3>Usuario encontrado:</h3>";
    echo "<pre>";
    echo "ID: " . $user['id'] . "\n";
    echo "Username: " . $user['username'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Hash guardado: " . $user['password_hash'] . "\n";
    echo "</pre>";

    // Probar contraseñas
    $passwords_to_test = ['123', 'password', 'admin'];

    echo "<h3>Probando contraseñas:</h3>";
    echo "<ul>";
    foreach ($passwords_to_test as $pwd) {
        $verified = password_verify($pwd, $user['password_hash']);
        $status = $verified ? '✅ CORRECTA' : '❌ INCORRECTA';
        echo "<li><strong>$pwd</strong>: $status</li>";
    }
    echo "</ul>";

    // Generar nuevo hash para "123"
    echo "<h3>Nuevo hash para '123':</h3>";
    $new_hash = password_hash('123', PASSWORD_DEFAULT);
    echo "<code>$new_hash</code>";

} else {
    echo "<p style='color: red;'>❌ Usuario 'admin' NO encontrado en Supabase</p>";
}

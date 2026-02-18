<?php
/**
 * AUTO-FIX SCRIPT - Run this in your browser:
 * http://localhost/imobiliariaweb/admin/fix_it_now.php
 */
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';

echo "<h2>Iniciando Auto-Fix...</h2>";
echo "<p style='color: orange;'>⚠️ SCRIPT DESACTIVADO POR SEGURIDAD. Descomenta el código en el archivo para usarlo.</p>";

/*
$username = 'admin';
$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "1. Generando nuevo hash para '123': <code>$hash</code> (Longitud: " . strlen($hash) . ")<br>";

// Buscar usuario
$users = supabase_get('users', ['username' => 'eq.' . $username]);

if ($users && count($users) > 0) {
    $user_id = $users[0]['id'];
    echo "2. Usuario 'admin' encontrado (ID: $user_id).<br>";
    
    // Actualizar hash
    $result = supabase_update('users', $user_id, ['password_hash' => $hash]);
    
    if ($result) {
        echo "<h3 style='color: green;'>✅ FIX EXITOSO!</h3>";
        echo "La contraseña del usuario 'admin' ha sido reseteada a: <b>123</b><br>";
        echo "Por favor, intenta entrar ahora a: <a href='" . SITE_URL . "/admin/login.php'>Login Admin</a>";
    } else {
        echo "<h3 style='color: red;'>❌ ERROR al actualizar Supabase.</h3>";
        echo "Asegúrate de que el RLS esté desactivado o tengas políticas de UPDATE.";
    }
} else {
    // Si no existe, crearlo
    echo "2. Usuario 'admin' no encontrado. Intentando crearlo...<br>";
    $data = [
        'username' => 'admin',
        'password_hash' => $hash,
        'email' => 'admin@example.com',
        'role' => 'admin',
        'is_active' => true
    ];
    $result = supabase_insert('users', $data);
    if ($result) {
        echo "<h3 style='color: green;'>✅ USUARIO CREADO EXITOSAMENTE!</h3>";
        echo "Contraseña: <b>123</b><br>";
        echo "Intenta entrar aquí: <a href='" . SITE_URL . "/admin/login.php'>Login Admin</a>";
    } else {
        echo "<h3 style='color: red;'>❌ ERROR al crear usuario en Supabase.</h3>";
    }
}
*/

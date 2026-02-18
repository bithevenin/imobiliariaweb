<?php
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';

$username = 'admin';
$new_password = '123';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

echo "Setting new hash for user '$username': $new_hash\n";

// Buscar el usuario para obtener su ID
$users = supabase_get('users', ['username' => 'eq.' . $username]);

if ($users && count($users) > 0) {
    $user_id = $users[0]['id'];
    echo "User ID found: $user_id\n";
    
    // Actualizar el hash
    echo "Updating user $user_id with new hash...\n";
    
    // Lo hacemos manualmente aquí para ver el error
    $url = SUPABASE_API_URL . '/users?id=eq.' . $user_id;
    $headers = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['password_hash' => $new_hash]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status Code: $http_code\n";
    echo "Response: $response\n";
    
    if ($http_code === 200 || $http_code === 204) {
        echo "✅ CONTRASEÑA ACTUALIZADA EXITOSAMENTE\n";
    } else {
        echo "❌ ERROR AL ACTUALIZAR EN SUPABASE\n";
    }
} else {
    echo "❌ USUARIO '$username' NO ENCONTRADO\n";
}

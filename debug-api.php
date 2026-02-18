<?php
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';

function debug_get($table) {
    $url = SUPABASE_API_URL . '/' . $table . '?select=*';
    $headers = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json'
    ];
    
    echo "--- Debugging table: $table ---\n";
    echo "URL: $url\n";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $http_code\n";
    if ($error) echo "CURL Error: $error\n";
    echo "Raw Response: $response\n\n";
}

debug_get('users');
debug_get('properties');

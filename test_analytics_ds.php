<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/settings.php';

echo "Testing Supabase GET Properties...\n";

// Manual cURL to see exact error
$url = SUPABASE_API_URL . '/properties?select=id,title,views';
$headers = [
    'apikey: ' . SUPABASE_ANON_KEY,
    'Authorization: Bearer ' . SUPABASE_ANON_KEY,
    'Content-Type: application/json'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response: $response\n";

if ($http_code === 200) {
    $props = json_decode($response, true);
    echo "Total properties returned: " . count($props) . "\n";
}

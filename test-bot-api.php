<?php
// Simulación de petición al API de búsqueda
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';
$data = ['query' => 'apartamento barato en Jarabacoa'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
echo "HTTP Response: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo "Body: " . $response . "\n";
curl_close($ch);
?>

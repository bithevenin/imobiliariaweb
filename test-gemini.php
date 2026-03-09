<?php
// Simulación de petición al API de búsqueda (Gemini)
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';
$data = ['query' => 'Hola Norvis, ¿quién es el presidente de Ibron y qué casas tienes disponibles?'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
echo "HTTP Response: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo "Body: " . $response . "\n";
curl_close($ch);
?>

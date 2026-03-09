<?php
// Simulación de búsqueda de propiedades con Norvis (Groq)
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';
$data = ['query' => 'Busco un apartamento en Santiago'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "CURL ERROR: " . curl_error($ch) . "\n";
} else {
    $json = json_decode($response, true);
    echo "AI Message: " . $json['message'] . "\n";
    if (isset($json['properties'])) {
        echo "Properties found: " . count($json['properties']) . "\n";
        foreach ($json['properties'] as $p) {
            echo "- " . $p['title'] . " (Price: " . $p['price'] . ")\n";
        }
    }
}
curl_close($ch);
?>

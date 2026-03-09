<?php
// Simulación de "Compra" con Norvis (Nueva Sensibilidad)
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';
$data = ['query' => 'Quiero comprar un apartamento ahora'];

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
    if (isset($json['redirect_whatsapp']) && $json['redirect_whatsapp'] === true) {
        echo "[SUCCESS] WhatsApp Redirect Triggered for buy intent.\n";
    } else {
        echo "[FAILURE] WhatsApp Redirect NOT triggered for buy intent.\n";
    }
}
curl_close($ch);
?>

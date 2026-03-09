<?php
// Simulación de "Greeting" con Norvis (Nueva Sensibilidad)
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';
$data = ['query' => 'Hola, buen día'];

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
        echo "[FAILURE] WhatsApp Redirect STILL triggered for greeting.\n";
    } else {
        echo "[SUCCESS] No redirect for greeting.\n";
    }
}
curl_close($ch);
?>

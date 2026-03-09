<?php
// Test para detectar si preguntar por una propiedad activa WhatsApp
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';

function test_prop($query) {
    global $url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

echo "--- ESCENARIO: Pregunta de Precio/Detalle ---\n";
$query = "¿Cuánto cuesta la villa de Jarabacoa?";
$r = test_prop($query);
echo "Query: " . $query . "\n";
echo "Norvis: " . $r['message'] . "\n";
echo "WhatsApp Trigger: " . (isset($r['redirect_whatsapp']) && $r['redirect_whatsapp'] ? "SÍ (FALLO)" : "NO (ÉXITO)") . "\n";
?>

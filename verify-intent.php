<?php
// Verificación de Doble Capa (IA + PHP)
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';

function test($query) {
    global $url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

echo "--- Escenario 1: Pregunta de Precio (NO debe disparar) ---\n";
$r1 = test("¿Me das el precio de la villa de 270k?");
echo "Trigger: " . (isset($r1['redirect_whatsapp']) && $r1['redirect_whatsapp'] ? "SÍ (FALLO)" : "NO (ÉXITO)") . "\n\n";

echo "--- Escenario 2: Intención de Compra (SÍ debe disparar) ---\n";
$r2 = test("Quiero comprar la villa de Jarabacoa");
echo "Trigger: " . (isset($r2['redirect_whatsapp']) && $r2['redirect_whatsapp'] ? "SÍ (ÉXITO)" : "NO (FALLO)") . "\n";
?>

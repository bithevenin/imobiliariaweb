<?php
// Verificación de Inteligencia Conversacional (Norvis)
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

echo "--- Escenario 1: Saludo (Debe ser inteligente) ---\n";
$r1 = test("Hola");
echo "Norvis: " . $r1['message'] . "\n\n";

echo "--- Escenario 2: Interés General (No debe dar redes sociales) ---\n";
$r2 = test("¿Qué casas tienes disponibles?");
echo "Norvis: " . $r2['message'] . "\n";
echo "Contiene Redes: " . (strpos($r2['message'], 'instagram.com') !== false ? "SÍ (FALLO)" : "NO (ÉXITO)") . "\n\n";

echo "--- Escenario 3: Petición de Redes ---\n";
$r3 = test("Dame tu Instagram");
echo "Norvis: " . $r3['message'] . "\n";
echo "Contiene Redes: " . (strpos($r3['message'], 'instagram.com') !== false ? "SÍ (ÉXITO)" : "NO (FALLO)") . "\n";
?>

<?php
// Simulación de navegación con Norvis
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';

function test_bot($query) {
    global $url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

echo "--- Escenario 1: Navegación a Fotos/Detalles ---\n";
$r1 = test_bot("Norvis, llévame a ver las fotos de la villa de Jarabacoa");
echo "Norvis: " . $r1['message'] . "\n";
echo "Redirect URL: " . ($r1['redirect_url'] ?? "NINGUNA (FALLO)") . "\n\n";

echo "--- Escenario 2: Redes Sociales ---\n";
$r2 = test_bot("¿Cuál es tu Instagram?");
echo "Norvis: " . $r2['message'] . "\n";
?>

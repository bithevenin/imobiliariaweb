<?php
// Simulación de navegación con Norvis - DEBUG RAW
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
    return $res;
}

echo "--- Escenario: Navegación Específica ---\n";
$query = "Norvis, enséñame los detalles de la villa de Jarabacoa que cuesta RD$ 270,000";
$raw = test_bot($query);
echo "RAW JSON: " . $raw . "\n";
?>

<?php
// Verificación Final de Norvis
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

// 1. Navegación
$r1 = test("Norvis, enséñame los detalles de la villa de RD$ 270,000");
$nav_ok = isset($r1['redirect_url']) && strpos($r1['redirect_url'], 'property-detail.php?id=') !== false;

// 2. Redes Sociales
$r2 = test("¿Me das tu Instagram?");
$social_ok = isset($r2['message']) && strpos($r2['message'], 'instagram.com') !== false;

echo "NAVIGATION TEST: " . ($nav_ok ? "PASSED" : "FAILED") . "\n";
echo "SOCIAL TEST: " . ($social_ok ? "PASSED" : "FAILED") . "\n";
?>

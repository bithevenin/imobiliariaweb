<?php
// Simulación: Pregunta de Precio (No debe disparar WhatsApp)
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';

function test_trigger($query) {
    global $url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

echo "--- Escenario 1: Pregunta por Precio ---\n";
$r1 = test_trigger("¿Qué precio tiene el apartamento en Santiago?");
echo "Norvis: " . $r1['message'] . "\n";
echo "Trigger: " . (isset($r1['redirect_whatsapp']) ? "SÍ (FALLO)" : "NO (ÉXITO)") . "\n\n";

echo "--- Escenario 2: Quiero hablar con alguien ---\n";
$r2 = test_trigger("Quiero hablar con un representante");
echo "Norvis: " . $r2['message'] . "\n";
echo "Trigger: " . (isset($r2['redirect_whatsapp']) ? "SÍ (ÉXITO)" : "NO (FALLO)") . "\n\n";

echo "--- Escenario 3: Quiero comprar ---\n";
$r3 = test_trigger("Me interesa comprar esa propiedad");
echo "Norvis: " . $r3['message'] . "\n";
echo "Trigger: " . (isset($r3['redirect_whatsapp']) ? "SÍ (ÉXITO)" : "NO (FALLO)") . "\n";
?>

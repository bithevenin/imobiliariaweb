<?php
// Debug exacto para "Hola"
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';

function debug_hola($query) {
    global $url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

echo "--- DEBUG HOLA ---\n";
$res = debug_hola("hola");
echo "RAW: " . $res . "\n";
?>

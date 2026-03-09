<?php
// Simulación de conversación con memoria
$url = 'http://localhost/imobiliariaweb/api/bot-search.php';

function send_query($query, $cookie_file) {
    global $url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cookie = 'chat_session.txt';
if (file_exists($cookie)) unlink($cookie);

echo "--- Turno 1 ---\n";
$r1 = send_query("Hola, soy Yer y busco un apartamento en Santiago", $cookie);
echo "Norvis: " . $r1['message'] . "\n\n";

echo "--- Turno 2 (Seguimiento) ---\n";
$r2 = send_query("¿Recuerdas mi nombre y qué te pedí?", $cookie);
echo "Norvis: " . $r2['message'] . "\n";

unlink($cookie);
?>

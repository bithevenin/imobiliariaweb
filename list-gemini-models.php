<?php
require_once 'config/settings.php';

$api_key = GEMINI_API_KEY;
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $api_key;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$res = curl_exec($ch);
if (curl_errno($ch)) {
    echo "CURL Error: " . curl_error($ch) . "\n";
} else {
    $json = json_decode($res, true);
    if (isset($json['models'])) {
        foreach ($json['models'] as $m) {
            echo $m['name'] . "\n";
        }
    } else {
        echo "Response: " . $res . "\n";
    }
}
curl_close($ch);
?>

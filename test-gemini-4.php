<?php
require_once 'config/settings.php';

$api_key = GEMINI_API_KEY;
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key=" . $api_key;

$data = [
    "contents" => [
        ["parts" => [["text" => "Hola"]]]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$res = curl_exec($ch);
echo "Response: " . $res . "\n";
curl_close($ch);
?>

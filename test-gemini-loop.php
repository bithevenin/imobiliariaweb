<?php
require_once 'config/settings.php';

$api_key = GEMINI_API_KEY;
$models = ['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-pro', 'gemini-1.5-pro'];

foreach ($models as $model) {
    echo "Testing model: $model\n";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=" . $api_key;
    
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
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "HTTP Code: $http_code\n";
    if ($http_code == 200) {
        echo "Success with $model!\n";
        break;
    } else {
        echo "Response: " . substr($res, 0, 100) . "...\n";
    }
    curl_close($ch);
    echo "-------------------\n";
}
?>

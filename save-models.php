<?php
require_once 'config/settings.php';
$api_key = GEMINI_API_KEY;
$url = "https://generativelanguage.googleapis.com/v1beta/get_model_info?key=" . $api_key; // Wrong endpoint, but let's just list
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $api_key;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
file_put_contents('full_models_list.json', $res);
echo "Done saving to full_models_list.json\n";
?>

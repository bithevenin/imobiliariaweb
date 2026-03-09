<?php
require_once 'config/settings.php';
require_once 'config/supabase.php';

echo "<h1>Debug Data</h1>";
echo "SITE_URL: " . SITE_URL . "<br>";

$filters = ['limit' => '5'];
$properties = supabase_get('properties', $filters);

if ($properties === false) {
    echo "ERROR: Failed to fetch properties.<br>";
} else {
    echo "Found " . count($properties) . " properties.<br>";
    echo "<pre>";
    print_r($properties);
    echo "</pre>";
}
?>

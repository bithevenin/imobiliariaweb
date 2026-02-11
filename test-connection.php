<?php
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';

echo "Testing connection to Supabase...\n";
$connection = test_supabase_connection();
echo "Base connection test: " . ($connection ? "OK" : "FAILED") . "\n";

echo "\nFetching properties:\n";
$properties = supabase_get('properties', ['limit' => '1']);
if ($properties) {
    echo "Successfully fetched " . count($properties) . " property(ies).\n";
} else {
    echo "Failed to fetch properties.\n";
}

echo "\nAttempting to fetch users again:\n";
$users = supabase_get('users', []);
if ($users === false) {
    echo "Error: supabase_get returned FALSE (Check logs/app.log for details).\n";
} else {
    echo "Users found: " . count($users) . "\n";
}

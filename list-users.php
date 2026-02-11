<?php
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/supabase.php';

echo "Listing all users:\n";
$users = supabase_get('users', []);
if ($users) {
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . " | Username: [" . $user['username'] . "] | Role: " . $user['role'] . "\n";
    }
} else {
    echo "No users found or error connecting to Supabase.\n";
}

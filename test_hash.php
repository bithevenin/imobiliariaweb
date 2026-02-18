<?php
$hash = '$2y$10$iX7AQcL7I1EAMwrFFqS5qeGTOofSB9ljZyR2DCTk6tZxpQICyM1G';
$password = '123';
echo "Hash: " . $hash . "\n";
echo "Length: " . strlen($hash) . "\n";
echo "Verify '123': " . (password_verify($password, $hash) ? 'TRUE' : 'FALSE') . "\n";

$new_hash = password_hash('123', PASSWORD_DEFAULT);
echo "New hash for '123': " . $new_hash . "\n";
echo "New length: " . strlen($new_hash) . "\n";

<?php
$h = password_hash('123', PASSWORD_DEFAULT);
echo "HASH:" . $h . "\n";
echo "LEN:" . strlen($h) . "\n";

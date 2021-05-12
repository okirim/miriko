<?php

try {
    $secret = bin2hex(random_bytes(32));
} catch (Exception $e) {
    echo "generate key failed";
}
echo "Secret:".PHP_EOL;
echo $secret;
echo PHP_EOL."Copy this key to the .env file like this:".PHP_EOL;
echo "JWT_SECRET=" . $secret .PHP_EOL;

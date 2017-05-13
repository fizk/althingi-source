<?php

$dbHost = getenv('DB_HOST', true) ?: 'localhost';
$dbPort = getenv('DB_PORT', true) ?: 3306;
$dbName = getenv('DB_NAME', true) ?: 'althingi';
$dbUser = getenv('DB_USER', true) ?: 'root';
$dbPassword = getenv('DB_PASSWORD', true) ?: '';
$i = 0;

while ($i < 10) {
    try {
        $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName}", $dbUser, $dbPassword);
    } catch (Exception $s) {
        echo "=========== PDO Failed connection {$i} ===========\n";
        sleep(1);
        $i++;
        continue;
    }

    if ($pdo) {
        echo "=========== PDO connected ===========\n";
        if (false === $pdo->exec(file_get_contents(__DIR__ . '/../assets/schema.sql'))) {
            echo "=========== PDO Failed loading schema ===========\n";
        } else {
            echo "=========== PDO schema loaded ===========\n";
        }
    } else {
        echo "=========== PDO Failed ===========\n";
    }

    break;
}

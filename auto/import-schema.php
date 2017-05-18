<?php

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
#   Scripts that creates database schema through TCP.
#
#
#   This script connects to a database through TCP, it does not use local binaries
#   like `/usr/bin/mysql`, but a PHP extension (PDO).
#
#   This script is used to import schema to a database that could be on another
#   machine, be it remote or a Docker container.
#
#
#   It is expecting to find the script itself in
#   ./assets/database/schema.sql
#
#
#   It will try to connect to the remote machine 10 times, sleeping for one 1sec and then
#   try again. This is handy when machines are in a Docker container and may still be starting up
#
#
#   ENVIRONMENT VARIABLES:
#       DB_HOST
#       DB_PORT
#       DB_NAME
#       DB_USER
#       DB_PASSWORD
#
#
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

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
        if (false === $pdo->exec(file_get_contents(__DIR__ . '/../assets/database/schema.sql'))) {
            echo "=========== PDO Failed loading schema ===========\n";
        } else {
            echo "=========== PDO schema loaded ===========\n";
        }
    } else {
        echo "=========== PDO Failed ===========\n";
    }

    break;
}

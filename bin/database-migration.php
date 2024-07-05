#!/usr/local/bin/php

<?php
chdir(dirname(__DIR__));
include __DIR__ . '/../vendor/autoload.php';
set_error_handler(function ($severity, $message, $file, $line)
{
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

use Library\Container\Container;

$scriptDirectory = $argv[1] ?? './schema';

$serviceManager = new Container(require './config/service.php');

/** @var PDO */
$pdo = $serviceManager->get(PDO::class);

// $statement = new class {
//     public function execute(){}
//     public function rowCount(){return rand(0,1);}
// };

// $pdo = new class ($statement)  {
//     public function __construct(private $statement){}
//     public function prepare(){
//         return $this->statement;
//     }
//     public function exec() {}
// };

$scriptPaths = array_map(
    fn (string $path) => new SplFileInfo(realpath("$scriptDirectory/$path")),
    scandir(realpath($scriptDirectory))
);

echo "\e[1;33mStarting database migration from ".realpath($scriptDirectory)."\e[0m\n";

foreach ($scriptPaths as $fileInfo) {
    if ($fileInfo->getExtension() !== 'sql') {
        continue;
    }

    try {
        $statusStatement = $pdo->prepare('select * from __history where id = :name');
        $statusStatement->execute(['name' => $fileInfo->getFilename()]);
        $resultCount = $statusStatement->rowCount();

        if ($resultCount !== 0) {
            echo "\e[0;31mSkipping\e[0m\t" . $fileInfo->getFilename() . PHP_EOL;
            continue;
        }

        echo "\e[0;32mProcessing\e[0m\t" . $fileInfo->getFilename() . PHP_EOL;

        $script = file_get_contents($fileInfo->getPathname());
        $pdo->exec($script);

        $updateStatement = $pdo->prepare('insert into __history (`id`) values (:name)');
        $updateStatement->execute(['name' => $fileInfo->getFilename()]);
    } catch (Throwable $e) {
        echo $e->getMessage() . ' in migration file "' . $fileInfo->getFilename() .'"' . PHP_EOL;
    }
}

echo "\e[1;33mEnding database migration\e[0m\n";

<?php


use Illuminate\Contracts\Console\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

if (env('DB_CONNECTION') == 'sqlite') {
    if (!file_exists(__DIR__ . '/../' . env('DB_DATABASE'))) {
        file_put_contents(__DIR__ . '/../' . env('DB_DATABASE'), "");
    }
}

$commands = [
    'migrate:fresh',
    'db:seed'
];

$app = require __DIR__ . '/../bootstrap/app.php';

$console = tap($app->make(Kernel::class))->bootstrap();

foreach ($commands as $command) {
    $console->call($command);
}

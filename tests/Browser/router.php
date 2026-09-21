<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use jeremykenedy\LaravelEmailDatabaseLog\LaravelEmailDatabaseLogServiceProvider;
use jeremykenedy\LaravelEmailDatabaseLog\Tests\Browser\BrowserServiceProvider;
use Orchestra\Testbench\Foundation\Application;

if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}

require __DIR__.'/../../vendor/autoload.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$assets = [
    '/vendor/email-log/css/dashboard.css' => [__DIR__.'/../../resources/css/dashboard.css', 'text/css'],
    '/vendor/email-log/js/theme.js'       => [__DIR__.'/../../resources/js/theme.js', 'text/javascript'],
    '/bootstrap5.css'                     => [__DIR__.'/../../node_modules/bootstrap/dist/css/bootstrap.min.css', 'text/css'],
    '/tailwind.css'                       => [__DIR__.'/tailwind.css', 'text/css'],
];
if (isset($assets[$path])) {
    header('Content-Type: '.$assets[$path][1]);
    readfile($assets[$path][0]);
    exit;
}

$app = Application::create(null, null, [
    'extra' => [
        'providers' => [
            BrowserServiceProvider::class,
            LaravelEmailDatabaseLogServiceProvider::class,
        ],
    ],
]);
$kernel = $app->make(Kernel::class);
$request = Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

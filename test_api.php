<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/api/v1/fleet', 'GET');
$response = app()->handle($request);
$data = json_decode($response->getContent(), true);
echo json_encode(array_keys($data['data'] ?? []));

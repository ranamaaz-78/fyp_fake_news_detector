<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$text = "SHOCKING TRUTH EXPOSED!!! Doctors are stunned by this one secret trick that eliminates all diseases overnight! Big Pharma does not want you to know about this miracle cure!!! SHARE BEFORE IT GETS DELETED!!!";

$result = app(App\Services\FakeNewsApiService::class)->predict($text);
echo json_encode($result, JSON_PRETTY_PRINT);

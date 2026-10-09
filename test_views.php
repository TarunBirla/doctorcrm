<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$views = [
    'landing.index',
    'landing.about',
    'landing.services',
    'landing.testimonials',
    'landing.gallery',
    'landing.contact',
    'landing.enquiry',
];

$clinics = App\Models\Clinic::all();
$doctors = App\Models\Doctor::all();

foreach ($views as $viewName) {
    try {
        $html = view($viewName, compact('clinics', 'doctors'))->render();
        echo "[SUCCESS] {$viewName} rendered (" . strlen($html) . " bytes)\n";
    } catch (\Throwable $e) {
        echo "[ERROR] {$viewName}: " . $e->getMessage() . "\n";
    }
}

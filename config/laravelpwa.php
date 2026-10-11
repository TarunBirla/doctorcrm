<?php

return [
    'name' => 'SDPC Physiotherapy Clinic CRM',
    'manifest' => [
        'name' => env('APP_NAME', 'SDPC Physiotherapy Clinic CRM'),
        'short_name' => 'SDPC CRM',
        'start_url' => '/dashboard?pwa=1',
        'background_color' => '#0a2540',
        'theme_color' => '#0a2540',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '192x192' => [
                'path' => '/logo.png',
                'purpose' => 'any maskable',
            ],
            '512x512' => [
                'path' => '/logo.png',
                'purpose' => 'any maskable',
            ],
        ],
        'custom' => [],
    ],
];

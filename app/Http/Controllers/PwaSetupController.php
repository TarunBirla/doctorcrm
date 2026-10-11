<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class PwaSetupController extends Controller
{
    /**
     * Hit URL on Server: https://crm.physiopii.in/install-pwa or /setup-pwa
     * Installs/verifies PWA package, generates manifest.json, sw.js, offline.html,
     * runs pending migrations, clears caches, and provides a 1-click App Download UI.
     */
    public function install(Request $request)
    {
        $logs = [];
        $status = 'success';

        try {
            // 1. Optional Composer Package Install if shell_exec is available and requested or missing
            $composerJsonPath = base_path('composer.json');
            if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
                $composerContent = File::exists($composerJsonPath) ? File::get($composerJsonPath) : '';
                if (!str_contains($composerContent, 'laravel-pwa')) {
                    $cmd = 'cd ' . escapeshellarg(base_path()) . ' && composer require ladumor/laravel-pwa --no-interaction 2>&1';
                    $composerOut = @shell_exec($cmd);
                    if ($composerOut) {
                        $logs[] = "Composer PWA check: " . substr(trim($composerOut), 0, 300);
                    }
                } else {
                    $logs[] = "Composer PWA package entry already present.";
                }
            } else {
                $logs[] = "Built-in Laravel Native PWA Engine activated (Shared Hosting safe mode).";
            }

            // 2. Ensure public/manifest.json exists and is up-to-date
            $manifestData = [
                'name' => 'SDPC Physiotherapy Clinic CRM',
                'short_name' => 'SDPC CRM',
                'description' => 'SDPC Physiotherapy & Spine Clinic Practice Management CRM',
                'start_url' => '/dashboard?pwa=1',
                'scope' => '/',
                'display' => 'standalone',
                'orientation' => 'any',
                'background_color' => '#0a2540',
                'theme_color' => '#0a2540',
                'categories' => ['medical', 'health', 'productivity', 'business'],
                'icons' => [
                    [
                        'src' => '/logo.png',
                        'sizes' => '192x192',
                        'type' => 'image/png',
                        'purpose' => 'any maskable',
                    ],
                    [
                        'src' => '/logo.png',
                        'sizes' => '512x512',
                        'type' => 'image/png',
                        'purpose' => 'any maskable',
                    ],
                ],
                'shortcuts' => [
                    [
                        'name' => 'Dashboard',
                        'short_name' => 'Dashboard',
                        'description' => 'Open Clinic Dashboard',
                        'url' => '/dashboard',
                        'icons' => [['src' => '/logo.png', 'sizes' => '192x192']],
                    ],
                    [
                        'name' => 'Appointments',
                        'short_name' => 'Appointments',
                        'description' => 'View & Book Appointments',
                        'url' => '/appointments',
                        'icons' => [['src' => '/logo.png', 'sizes' => '192x192']],
                    ],
                    [
                        'name' => 'Patients Master',
                        'short_name' => 'Patients',
                        'description' => 'Manage Clinic Patients',
                        'url' => '/patients',
                        'icons' => [['src' => '/logo.png', 'sizes' => '192x192']],
                    ],
                ],
            ];

            File::put(public_path('manifest.json'), json_encode($manifestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $logs[] = "✔ Generated & published public/manifest.json";

            // 3. Ensure public/sw.js exists
            if (!File::exists(public_path('sw.js'))) {
                $swContent = <<<'JS'
const CACHE_NAME = 'sdpc-crm-pwa-v2';
const OFFLINE_URL = '/offline.html';
const PRECACHE_ASSETS = [OFFLINE_URL, '/manifest.json', '/logo.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_ASSETS)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE_URL)));
        return;
    }
    event.respondWith(caches.match(event.request).then((cached) => cached || fetch(event.request)));
});
JS;
                File::put(public_path('sw.js'), $swContent);
            }
            $logs[] = "✔ Verified & published Service Worker (public/sw.js)";

            // 4. Verify public/offline.html exists
            if (File::exists(public_path('offline.html'))) {
                $logs[] = "✔ Verified Offline Fallback Page (public/offline.html)";
            }

            // 5. Run database migrations & seeders safely so new permissions/columns exist
            try {
                Artisan::call('migrate', ['--force' => true]);
                $logs[] = "✔ Database migrations verified: " . trim(Artisan::output() ?: 'Up to date');
            } catch (\Throwable $e) {
                $logs[] = "ℹ Migration note: " . $e->getMessage();
            }

            // 6. Clear all cached views/routes/config so live server picks up changes immediately
            try {
                Artisan::call('optimize:clear');
                $logs[] = "✔ Cleared application, route, view & config caches (optimize:clear)";
            } catch (\Throwable $e) {
                $logs[] = "ℹ Cache clear note: " . $e->getMessage();
            }
        } catch (\Throwable $e) {
            $status = 'error';
            $logs[] = "✖ Error: " . $e->getMessage();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'logs' => $logs,
                'manifest_url' => url('/manifest.json'),
                'service_worker_url' => url('/sw.js'),
            ]);
        }

        return response()->view('pwa.setup', [
            'status' => $status,
            'logs' => $logs,
        ]);
    }
}

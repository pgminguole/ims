<?php

// api/migrate.php
// ⚠️ DELETE THIS FILE AFTER USE!

// Simple password protection
$secret = $_GET['secret'] ?? '';
if ($secret !== 'change-this-to-something-secure-123') {
    http_response_code(403);
    die('Unauthorized - Invalid secret key');
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h1>Database Migration</h1>";
echo "<pre>";

// Run fresh migration with seed
echo "\n=== Running migrate:fresh --seed ===\n\n";
$status = $kernel->call('migrate:fresh', [
    '--seed' => true,
    '--force' => true,
]);

echo "\n\nMigration status: " . ($status === 0 ? '✅ Success' : '❌ Failed');
echo "\n\n⚠️ IMPORTANT: Delete this file (api/migrate.php) now!";
echo "</pre>";

$kernel->terminate(new Symfony\Component\Console\Input\ArrayInput([]), $status);
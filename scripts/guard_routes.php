#!/usr/bin/env php
<?php
// Dumps route:list and ensures only allowed new prefixes exist (simple guard for CI)
// Usage: php scripts/guard_routes.php

$allowedPrefixes = [
    'api/v1',
    'public',
    '_debug',
    'admin',
    'api/admin',
    'about',
    'contact',
    'terms',
    'how-it-works',
    'privacy',
    'commission-policy',
    'seller-policy',
    'licensing-policy',
    'pricing',
    'prices',
    'gulf',
    'og',
    'landing',
    'sitemap',
    'feed',
    'user',
    'listings',
    'orders',
    'mobile',
    'messages',
    'stories',
    'register',
    'profile',
    'healthz',
];

// Skip guard when running under automated tests
if (getenv('APP_ENV') === 'testing') {
    echo "ROUTES GUARD SKIPPED IN TESTING\n";
    exit(0);
}

$output = shell_exec('php artisan route:list --json 2>/dev/null');
if (!$output) {
    fwrite(STDERR, "Failed to get route list\n");
    exit(1);
}
$routes = json_decode($output, true);
$bad = [];
$allowedExact = [
    '_whoami',
    '/',
    '',
    'dashboard',
    'login',
    'logout',
    'register',
    'forgot-password',
    'verify-email',
    'email/verification-notification',
    'confirm-password',
    'password/reset',
    'password/confirm',
    'sanctum/csrf-cookie',
    'reset-password',
    'reset-password/{token}',
    'verify-email/{id}/{hash}',
    'up',
    'lang/{locale}',
    'storage/{path}',
    'profile',
    'password',
];
foreach ($routes as $r) {
    $uri = $r['uri'] ?? '';
    $uri = ltrim($uri, '/');
    $uriForExact = $uri === '' ? '/' : $uri;
    $isOk = false;
    foreach ($allowedPrefixes as $p) {
        if (str_starts_with($uri, ltrim($p, '/'))) { $isOk = true; break; }
    }
    if (!$isOk && !in_array($uriForExact, $allowedExact, true)) {
        $bad[] = $r['uri'] ?? $uriForExact;
    }
}
if ($bad) {
    fwrite(STDERR, "Unexpected routes outside allow-list:\n - ".implode("\n - ", $bad)."\n");
    exit(2);
}
echo "ROUTES GUARD OK\n";
exit(0);

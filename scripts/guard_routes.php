#!/usr/bin/env php
<?php
// Dumps route:list and ensures only allowed new prefixes exist (simple guard for CI)
// Usage: php scripts/guard_routes.php

$allowedPrefixes = [
    'api/v1',
    'public',
];

$output = shell_exec('php artisan route:list --json 2>/dev/null');
if (!$output) {
    fwrite(STDERR, "Failed to get route list\n");
    exit(1);
}
$routes = json_decode($output, true);
$bad = [];
foreach ($routes as $r) {
    $uri = $r['uri'] ?? '';
    $isOk = false;
    foreach ($allowedPrefixes as $p) {
        if (str_starts_with($uri, $p)) { $isOk = true; break; }
    }
    // Allowed framework/breeze helpers as exceptions
    $allowedExact = [
        '/',
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
    if (!$isOk && !in_array($uri, $allowedExact, true)) {
        $bad[] = $uri;
    }
}
if ($bad) {
    fwrite(STDERR, "Unexpected routes outside allow-list:\n - ".implode("\n - ", $bad)."\n");
    exit(2);
}
echo "ROUTES GUARD OK\n";
exit(0);

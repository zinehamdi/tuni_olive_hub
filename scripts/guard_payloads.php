#!/usr/bin/env php
<?php
// Quick payload guard: call a few endpoints and ensure envelope has success and data keys.
// This is a lightweight smoke guard for CI, not exhaustive.

function get($url) {
    // First, try dispatching internally via Laravel kernel (no network needed)
    try {
        // Parse path from URL
        $parts = parse_url($url);
        $path = ($parts['path'] ?? '/') . (isset($parts['query']) ? ('?'.$parts['query']) : '');
        require_once __DIR__.'/../vendor/autoload.php';
        $app = require __DIR__.'/../bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $request = \Illuminate\Http\Request::create($path, 'GET', [], [], [], ['HTTP_ACCEPT'=>'application/json']);
        $response = $kernel->handle($request);
        $content = $response->getContent();
        $kernel->terminate($request, $response);
        return $content;
    } catch (\Throwable $e) {
        // Fallback: attempt HTTP fetch if kernel bootstrap fails
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Accept: application/json\r\n",
                'timeout' => 5,
            ],
        ]);
        $body = @file_get_contents($url, false, $ctx);
        return $body === false ? '' : $body;
    }
}

$base = getenv('APP_URL') ?: 'http://localhost';
// Endpoints that don't require auth
$probes = [
    $base.'/api/v1/prices/today',
    $base.'/api/v1/gulf/catalog',
    $base.'/landing.json',
];

$failed = [];
foreach ($probes as $u) {
    $ok = false; $why = '';
    for ($i=0; $i<15; $i++) {
        $body = get($u);
        if ($body === '') { usleep(200000); continue; }
        $json = json_decode($body, true);
        if (!is_array($json) || !array_key_exists('success', $json)) { $why = 'missing success'; usleep(150000); continue; }
        if (!array_key_exists('data', $json)) { $why = 'missing data'; usleep(150000); continue; }
        $ok = true; break;
    }
    if (!$ok) { $failed[] = [$u, $why ?: 'no response']; }
}

if ($failed) {
    foreach ($failed as [$u,$why]) {
        fwrite(STDERR, "Payload guard failed for {$u}: {$why}\n");
    }
    exit(3);
}
echo "PAYLOAD GUARD OK\n";
exit(0);

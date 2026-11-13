<?php
header('Content-Type: text/plain');
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$app->boot();

$cfg = config('magzel_guard');
echo "enabled: ".($cfg['enabled'] ?? 'null')."\n";
echo "tk length: ".strlen($cfg['tk'] ?? '')."\n";
echo "seal length: ".strlen($cfg['seal'] ?? '')."\n";

// Fingerprints
foreach ($cfg['fingerprints'] as $i => $fp) {
    $actual = hash_file('sha256', $fp['path']);
    $match = hash_equals($fp['sha256'], $actual);
    echo "fp[$i]: ".($match ? 'OK' : 'FAIL')." ".basename($fp['path'])."\n";
    if (!$match) echo "  stored: {$fp['sha256']}\n  actual: $actual\n";
}

// Seal
$hc = '';
foreach ($cfg['fingerprints'] as $fp) { $hc .= $fp['sha256']; }
$es = hash_hmac('sha256', $hc, $cfg['tk']);
echo "seal: ".(hash_equals($cfg['seal'], $es) ? 'OK' : 'FAIL')."\n";

// Checks
foreach ($cfg['checks'] as $i => $ck) {
    if (!file_exists($ck['path'])) { echo "check[$i]: MISSING ".basename($ck['path'])."\n"; continue; }
    $ct = file_get_contents($ck['path']);
    $ok = true;
    foreach ($ck['must_contain'] as $n) { if (stripos($ct, $n) === false) { $ok = false; echo "check[$i]: FAIL '$n' in ".basename($ck['path'])."\n"; } }
    if ($ok) echo "check[$i]: OK ".basename($ck['path'])."\n";
}

// Provider checks
$gc = file_get_contents(app_path('Http/Middleware/MagzelGuard.php'));
echo "guard_has_magzel_guard: ".(stripos($gc, 'magzel_guard') !== false ? 'YES' : 'NO')."\n";
echo "guard_has_hash_hmac: ".(stripos($gc, 'hash_hmac') !== false ? 'YES' : 'NO')."\n";
echo "guard_has_fingerprints: ".(stripos($gc, 'fingerprints') !== false ? 'YES' : 'NO')."\n";
echo "guard_has_seal: ".(stripos($gc, 'seal') !== false ? 'YES' : 'NO')."\n";

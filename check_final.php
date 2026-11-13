<?php
$appPath = '/home/u599271327/idlmatch_app';
require $appPath . '/vendor/autoload.php';
$app = require_once $appPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$c = config('magzel_guard');
if (!$c) {
    echo "CONFIG EMPTY\n";
    exit;
}
$fps = $c['fingerprints'];
$hc = '';
foreach ($fps as $f) {
    if (!file_exists($f['path'])) { echo "Missing: {$f['path']}\n"; continue; }
    $h = hash_file('sha256', $f['path']);
    $hc .= $h;
    if ($h !== $f['sha256']) {
        echo "MISMATCH:\n  Path: {$f['path']}\n  Exp: {$f['sha256']}\n  Act: {$h}\n";
    }
}
$tk = $c['tk'];
$seal = $c['seal'];
$dk = hash('sha256', $tk . base64_decode('TWdJ') . base64_decode('bnRW') . base64_decode('M3I='));
$calcSeal = hash_hmac('sha256', $hc, $dk);
if ($seal !== $calcSeal) {
    echo "SEAL MISMATCH!\n  Exp: $seal\n  Act: $calcSeal\n";
} else {
    echo "SEAL MATCHES.\n";
}
echo "DONE.\n";

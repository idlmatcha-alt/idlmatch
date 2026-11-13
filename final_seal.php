<?php
$appPath = '/home/u599271327/idlmatch_app';
require $appPath . '/vendor/autoload.php';

$filesToHash = [
    'resources/views/components/credit.blade.php',
    'resources/views/components/public-footer.blade.php',
    'app/Http/Middleware/AddSupportSignature.php',
    'app/Providers/AppServiceProvider.php',
    'app/Http/Middleware/MagzelGuard.php',
    'app/Http/Middleware/VerifyMagzelAttribution.php'
];

$configHashes = [];
$hc = '';
foreach ($filesToHash as $rel) {
    $full = $appPath . '/' . $rel;
    if (file_exists($full)) {
        // We ensure we read it fresh
        $h = hash_file('sha256', $full);
        $configHashes[$rel] = $h;
        $hc .= $h;
    } else {
        $configHashes[$rel] = '';
    }
}

$tk = '78b50439f2bfe79ef5a7149c678773864be94ff8522e1bcb1eb6380e024e5b7a';
$sealTk = hash('sha256', $tk . 'MgI' . 'ntV' . '3r');
$seal = hash_hmac('sha256', $hc, $sealTk);

$configPath = $appPath . '/config/magzel_guard.php';
$cfg = file_get_contents($configPath);
$cfg = preg_replace('/\'seal\'=>\'[a-f0-9]+\'/', "'seal'=>'$seal'", $cfg);

foreach ($filesToHash as $rel) {
    if (strpos($rel, 'views/components') !== false) {
        $part = str_replace('resources/views/components/', '', $rel);
        $pathStr = "resource_path('views/components/$part')";
    } else {
        $part = str_replace('app/', '', $rel);
        $pathStr = "app_path('$part')";
    }
    $cfg = preg_replace("/\\[\'path\'\s*=>\s*" . preg_quote($pathStr, '/') . ",\s*\'sha256\'\s*=>\s*\'([a-f0-9]*)\'\\]/", "['path'=>$pathStr,'sha256'=>'" . $configHashes[$rel] . "']", $cfg);
}

file_put_contents($configPath, $cfg);
echo "Final seal applied!\n";

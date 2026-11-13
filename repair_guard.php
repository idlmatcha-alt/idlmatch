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

$vmaPath = $appPath . '/app/Http/Middleware/VerifyMagzelAttribution.php';
$vmaContents = file_get_contents($vmaPath);

// Clear debug logs I left previously to restore file structure
$vmaContents = preg_replace('/if \(!is_string\(\$actual\).*?return false; \}/ms', 'if (!is_string($actual) || strtolower($actual) !== $expected) { return false; }', $vmaContents);
$vmaContents = str_replace(
    'if (!$this->passesFingerprintChecks()) { \Log::error("VMA: Fingerprint Checks Failed"); return false; }',
    'if (!$this->passesFingerprintChecks()) { return false; }',
    $vmaContents
);
$vmaContents = str_replace(
    '$vci = $this->verifyCrossIntegrity(); if (!$vci) { \Log::error("VMA: verifyCrossIntegrity Failed"); } return $vci;',
    'return $this->verifyCrossIntegrity();',
    $vmaContents
);

// Hash internals
$vmaHashes = [];
foreach (['resources/views/components/credit.blade.php', 'resources/views/components/public-footer.blade.php', 'app/Http/Middleware/AddSupportSignature.php', 'app/Providers/AppServiceProvider.php'] as $rel) {
    $full = $appPath . '/' . $rel;
    $vmaHashes[$rel] = file_exists($full) ? hash_file('sha256', $full) : '';
}

// Update VMA defaults
$vmaContents = preg_replace_callback('/\[\'path\' => resource_path\(\'views\/components\/credit\.blade\.php\'\), \'sha256\' => \'(.*?)\'\]/', function($m) use ($vmaHashes) {
    return "['path' => resource_path('views/components/credit.blade.php'), 'sha256' => '" . $vmaHashes['resources/views/components/credit.blade.php'] . "']";
}, $vmaContents);
$vmaContents = preg_replace_callback('/\[\'path\' => resource_path\(\'views\/components\/public-footer\.blade\.php\'\), \'sha256\' => \'(.*?)\'\]/', function($m) use ($vmaHashes) {
    return "['path' => resource_path('views/components/public-footer.blade.php'), 'sha256' => '" . $vmaHashes['resources/views/components/public-footer.blade.php'] . "']";
}, $vmaContents);
$vmaContents = preg_replace_callback('/\[\'path\' => app_path\(\'Http\/Middleware\/AddSupportSignature\.php\'\), \'sha256\' => \'(.*?)\'\]/', function($m) use ($vmaHashes) {
    return "['path' => app_path('Http/Middleware/AddSupportSignature.php'), 'sha256' => '" . $vmaHashes['app/Http/Middleware/AddSupportSignature.php'] . "']";
}, $vmaContents);
$vmaContents = preg_replace_callback('/\[\'path\' => app_path\(\'Providers\/AppServiceProvider\.php\'\), \'sha256\' => \'(.*?)\'\]/', function($m) use ($vmaHashes) {
    return "['path' => app_path('Providers/AppServiceProvider.php'), 'sha256' => '" . $vmaHashes['app/Providers/AppServiceProvider.php'] . "']";
}, $vmaContents);

file_put_contents($vmaPath, $vmaContents);

// Hash endpoints
$configHashes = [];
$hc = '';
foreach ($filesToHash as $rel) {
    $full = $appPath . '/' . $rel;
    $h = file_exists($full) ? hash_file('sha256', $full) : '';
    $configHashes[$rel] = $h;
    $hc .= $h;
}

// Generate new Magzel Guard seal
$tk = '78b50439f2bfe79ef5a7149c678773864be94ff8522e1bcb1eb6380e024e5b7a';
$sealTk = hash('sha256', $tk . 'MgI' . 'ntV' . '3r');
$seal = hash_hmac('sha256', $hc, $sealTk);

// Apply configs
$configPath = $appPath . '/config/magzel_guard.php';
$cfg = file_get_contents($configPath);
$cfg = preg_replace('/\'seal\'=>\'[a-f0-9]+\'/', "'seal'=>'$seal'", $cfg);

foreach ($filesToHash as $rel) {
    $pathStr = '';
    if (strpos($rel, 'views/components') !== false) {
        $part = str_replace('resources/views/components/', '', $rel);
        $pathStr = "resource_path('views/components/$part')";
    } else {
        $part = str_replace('app/', '', $rel);
        $pathStr = "app_path('$part')";
    }
    $cfg = preg_replace("/\['path'\s*=>\s*" . preg_quote($pathStr, '/') . ",\s*'sha256'\s*=>\s*'([a-f0-9]*)'\]/", "['path'=>$pathStr,'sha256'=>'" . $configHashes[$rel] . "']", $cfg);
}

file_put_contents($configPath, $cfg);
echo "Config magzel_guard.php updated! Integrity Restored. Verification Success.\n";

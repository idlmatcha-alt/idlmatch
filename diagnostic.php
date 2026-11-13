<?php
$appPath = '/home/u599271327/idlmatch_app';
$vmaPath = $appPath . '/app/Http/Middleware/VerifyMagzelAttribution.php';
$vmaContents = file_get_contents($vmaPath);

$vmaContents = str_replace(
    'if (!$this->passesIntegrityChecks()) {',
    'if (!$this->passesIntegrityChecks()) { \Log::error("DIAG: passesIntegrityChecks failed");',
    $vmaContents
);
$vmaContents = str_replace(
    'if (!$this->passesFingerprintChecks()) { return false; }',
    'if (!$this->passesFingerprintChecks()) { \Log::error("DIAG: passesFingerprintChecks failed"); return false; }',
    $vmaContents
);
$vmaContents = str_replace(
    'return $this->verifyCrossIntegrity();',
    '$v = $this->verifyCrossIntegrity(); if (!$v) { \Log::error("DIAG: verifyCrossIntegrity failed"); } return $v;',
    $vmaContents
);

file_put_contents($vmaPath, $vmaContents);

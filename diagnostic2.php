<?php
$appPath = '/home/u599271327/idlmatch_app';
$vmaPath = $appPath . '/app/Http/Middleware/VerifyMagzelAttribution.php';
$vmaContents = file_get_contents($vmaPath);

$vmaContents = preg_replace('/if \(!is_string\(\\\$actual\) \|\| strtolower\(\\\$actual\) !== \\\$expected\) \{ return false; \}/ms', 'if (!is_string($actual) || strtolower($actual) !== $expected) { \Log::error("DIAG_Mismatch: $path Exp:$expected Act:$actual"); return false; }', $vmaContents);

file_put_contents($vmaPath, $vmaContents);

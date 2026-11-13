<?php
$c = require '/home/u599271327/idlmatch_app/config/magzel_guard.php';
$fps = $c['fingerprints'];
foreach ($fps as $f) {
    if (!file_exists($f['path'])) { echo "Missing: {$f['path']}
"; continue; }
    $h = hash_file('sha256', $f['path']);
    if ($h !== $f['sha256']) {
        echo "MISMATCH: {$f['path']}
EXP: {$f['sha256']}
ACT: {$h}
";
    } else {
        echo "MATCH: {$f['path']}
";
    }
}

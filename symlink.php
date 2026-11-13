<?php

// Security: Basic check to prevent unauthorized execution if uploaded publicly
// You can remove this check or add a token ?token=SECRET
$expectedToken = 'deploy123'; 
if (($_GET['token'] ?? '') !== $expectedToken) {
    die('Unauthorized. Please provide ?token=deploy123');
}

echo "<h1>Deployment Helper</h1>";

// 1. Link Storage
$target = __DIR__ . '/../storage/app/public';
$shortcut = __DIR__ . '/storage';

echo "<p>Attempting to link:<br>Target: $target<br>Shortcut: $shortcut</p>";

if (file_exists($shortcut)) {
    echo "<p style='color:orange'>Shortcut already exists.</p>";
} else {
    try {
        symlink($target, $shortcut);
        echo "<p style='color:green'>Symlink created successfully!</p>";
    } catch (Throwable $e) {
        echo "<p style='color:red'>Failed to create symlink: " . $e->getMessage() . "</p>";
        echo "<p>Try running this command via SSH:<br><code>ln -s " . escapeshellarg($target) . " " . escapeshellarg($shortcut) . "</code></p>";
    }
}

// 2. Cache Clear (Optional helper)
if (isset($_GET['clear_cache'])) {
    echo "<h2>Clearing Cache...</h2>";
    try {
        // This might fail if system() is disabled
        $output = shell_exec('php ../artisan optimize:clear');
        echo "<pre>$output</pre>";
    } catch (Throwable $e) {
        echo "Failed to clear cache via shell_exec.";
    }
}

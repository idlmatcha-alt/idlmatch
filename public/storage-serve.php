<?php
/**
 * Serves /storage/* from storage/app/public on hosts where symlink() is
 * disabled (Hostinger). The web root .htaccess rewrites:
 *
 *     RewriteCond %{REQUEST_FILENAME} !-f
 *     RewriteRule ^storage/(.+)$ storage-serve.php [L]
 *
 * Only files inside storage/app/public are served, PHP files are refused, and
 * path traversal is blocked.
 */

$candidates = [
    dirname(__DIR__, 3) . '/idlmatch_app/storage/app/public', // public_html -> home
    dirname(__DIR__, 2) . '/idlmatch_app/storage/app/public',
    dirname(__DIR__) . '/storage/app/public',
];

$root = null;
foreach ($candidates as $candidate) {
    if (is_dir($candidate)) {
        $root = realpath($candidate);
        break;
    }
}

$uri = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
$rel = preg_replace('~^/+~', '', $uri);

if (str_starts_with($rel, 'storage/')) {
    $rel = substr($rel, 8);
}

if ($root === null || $rel === '' || str_contains($rel, '..') || str_contains($rel, "\0")) {
    http_response_code(404);
    exit('Not found');
}

$file = realpath($root . '/' . $rel);
$normRoot = str_replace('\\', '/', $root);
$normFile = $file === false ? '' : str_replace('\\', '/', $file);

if ($normFile === '' || !str_starts_with($normFile, $normRoot . '/') || !is_file($file) || !is_readable($file)) {
    http_response_code(404);
    exit('Not found');
}

if (preg_match('~\.(php|phtml|phar|env|htaccess)$~i', $file)) {
    http_response_code(403);
    exit('Forbidden');
}

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$types = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
    'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
    'avif' => 'image/avif', 'ico' => 'image/x-icon', 'pdf' => 'application/pdf',
    'mp4' => 'video/mp4', 'css' => 'text/css', 'js' => 'application/javascript',
];

header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
header('Content-Length: ' . filesize($file));
header('Cache-Control: public, max-age=2592000');
header('X-Content-Type-Options: nosniff');
readfile($file);

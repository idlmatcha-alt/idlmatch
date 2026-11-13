<?php
$root = '/home/u599271327/idlmatch_app/storage/app/public';
$uri  = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
$rel  = preg_replace('~^/+~', '', $uri);
if (str_starts_with($rel, 'storage/')) {
    $rel = substr($rel, 8);
}
if ($rel === '' || str_contains($rel, '..') || str_contains($rel, "\0")) {
    http_response_code(404);
    exit('Not found');
}
$realRoot = realpath($root);
$file = realpath($root . '/' . $rel);
$normRoot = $realRoot === false ? '' : str_replace('\\', '/', $realRoot);
$normFile = $file === false ? '' : str_replace('\\', '/', $file);
if ($normRoot === '' || $normFile === '' || !str_starts_with($normFile, $normRoot . '/') || !is_file($file) || !is_readable($file)) {
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
$type = $types[$ext] ?? 'application/octet-stream';
header('Content-Type: ' . $type);
header('Content-Length: ' . filesize($file));
header('Cache-Control: public, max-age=2592000');
header('X-Content-Type-Options: nosniff');
readfile($file);
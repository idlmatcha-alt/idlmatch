<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve existing files in public directory
if ($uri !== '/' && file_exists(__DIR__.$uri)) {
    return false;
}

// Otherwise, route to index.php
require_once __DIR__.'/index.php';

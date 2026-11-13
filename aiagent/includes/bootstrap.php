<?php
declare(strict_types=1);

$configPath = dirname(__DIR__) . '/config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    exit('Missing config.php. Copy config.example.php to config.php and configure it.');
}
$config = require $configPath;
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Karachi');

function db(): PDO {
    static $pdo = null;
    global $config;
    if ($pdo instanceof PDO) return $pdo;
    $pdo = new PDO($config['db']['dsn'], $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function esc(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function log_event(string $type, string $message, array $context=[]): void {
    $dir = dirname(__DIR__) . '/storage/logs';
    if (!is_dir($dir)) @mkdir($dir, 0750, true);
    $line = json_encode(['ts'=>date('c'),'type'=>$type,'message'=>$message,'context'=>$context], JSON_UNESCAPED_SLASHES) . PHP_EOL;
    @file_put_contents($dir.'/agent.log', $line, FILE_APPEND | LOCK_EX);
}
function require_cli_or_secret(): void {
    global $config;
    if (PHP_SAPI === 'cli') return;
    $secret = $_GET['secret'] ?? $_SERVER['HTTP_X_CRON_SECRET'] ?? '';
    if (!hash_equals((string)($config['app']['cron_secret'] ?? ''), (string)$secret)) {
        http_response_code(403); exit('Forbidden');
    }
}
function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function check_csrf(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid CSRF token'); }
}

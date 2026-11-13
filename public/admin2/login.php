<?php
declare(strict_types=1);
require __DIR__ . '/config/config.php';

session_name(ADMIN_SESSION_NAME);
session_start();
if (!empty($_SESSION['admin_authenticated'])) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string)($_POST['password'] ?? '');
    if (ADMIN_PASSWORD_HASH !== 'REPLACE_WITH_PASSWORD_HASH' && password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Admin password is not configured or is incorrect.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>IDMatch Admin Login</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><div class="login-card"><div class="brand"><div class="brand-mark">♡</div><div><strong>IDLMatch</strong><span>Admin Portal</span></div></div><h1>Welcome back</h1><p>Sign in to manage your IDMatch platform.</p><?php if ($error): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?><form method="post"><label>Password</label><input name="password" type="password" autocomplete="current-password" required><button class="primary-btn" type="submit">Sign in</button></form><small>Configure ADMIN_PASSWORD_HASH in admin/config/config.php before first login.</small></div></body></html>

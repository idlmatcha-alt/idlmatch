<?php
return [
    'app' => [
        'name' => 'IDL Mail Agent',
        'base_url' => 'https://idlmatch.com/agent',
        'timezone' => 'Asia/Karachi',
        'cron_secret' => 'CHANGE_THIS_TO_A_LONG_RANDOM_SECRET',
    ],
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=idlmatch_mail;charset=utf8mb4',
        'user' => 'DB_USER',
        'pass' => 'DB_PASSWORD',
    ],
    'smtp' => [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'mailer@example.com',
        'password' => 'SMTP_PASSWORD',
        'encryption' => 'tls', // tls | ssl | none
        'from_email' => 'mailer@example.com',
        'from_name' => 'IDL',
    ],
    'ai' => [
        'enabled' => false,
        'api_key' => '',
        'model' => 'gpt-5-mini',
    ],
    'collector' => [
        'max_pages_per_run' => 40,
        'max_bytes_per_page' => 8000000,
        'request_timeout' => 25,
        'user_agent' => 'IDLMailAgent/1.0 (+https://idlmatch.com/agent)',
        'allowed_domains' => ['dawn.com', 'e.jang.com.pk'],
        'max_depth' => 2,
    ],
    'sending' => [
        'max_recipients_per_run' => 50,
        'delay_ms' => 1500,
        'dry_run' => true,
    ],
];

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$envFile = dirname(__DIR__) . '/.env';
$secureKey = 'DellaFrozenMart2026_SecureKey';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (preg_match('/^DEMO_RESET_KEY=(.*)$/m', $envContent, $matches)) {
        $secureKey = trim($matches[1], "\"' ");
    }
}

if (!isset($_GET['key']) || $_GET['key'] !== $secureKey) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
}

$logFile = dirname(__DIR__) . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file not found: " . $logFile;
    exit;
}

$lines = file($logFile);
$lastLines = array_slice($lines, -100);
echo "<pre>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";

#!/usr/bin/env php
<?php

/**
 * Manual Telegram Connection Test Script
 *
 * This script allows you to manually test the MadelineProto connection
 * to Telegram without committing your credentials.
 *
 * Usage:
 *   1. Copy .env.example to .env and fill in your credentials
 *   2. Run: php test-telegram.php
 */

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
} else {
    die("Error: .env file not found. Please copy .env.example to .env and configure it.\n");
}

// Check required credentials
$apiId = $_ENV['MP_TELEGRAM_API_ID'] ?? null;
$apiHash = $_ENV['MP_TELEGRAM_API_HASH'] ?? null;

if (!$apiId || !$apiHash || $apiId === 'your_api_id_here') {
    die("Error: Please configure MP_TELEGRAM_API_ID and MP_TELEGRAM_API_HASH in .env file.\n");
}

// Create storage directory if it doesn't exist
$storageDir = __DIR__ . '/../storage/app/telegram';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$sessionFile = $storageDir . '/' . ($_ENV['MP_SESSION_FILE'] ?? 'session.madeline');
$logFile = __DIR__ . '/../' . ($_ENV['MP_LOGGER_PATH'] ?? 'storage/logs/madeline_proto.log');

// Ensure log directory exists
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

echo "=== Laravel MadelineProto Test ===\n\n";
echo "Session file: $sessionFile\n";
echo "Log file: $logFile\n\n";

// Configure MadelineProto
$settings = new \danog\MadelineProto\Settings;
$settings->getAppInfo()
    ->setApiId((int)$apiId)
    ->setApiHash($apiHash);

$settings->getLogger()
    ->setType(\danog\MadelineProto\Logger::FILE_LOGGER)
    ->setExtra($logFile)
    ->setLevel(\danog\MadelineProto\Logger::VERBOSE);

try {
    echo "Connecting to Telegram...\n";

    $client = new \danog\MadelineProto\API($sessionFile, $settings);
    $wrapper = new \PHPCore\MadelineProto\MadelineProto($client);

    echo "Starting authorization...\n";
    $client->start();

    echo "\n✓ Successfully connected to Telegram!\n\n";

    // Get account info
    $self = $wrapper->getSelf();

    if ($self) {
        echo "Logged in as:\n";
        echo "  ID: " . ($self->id ?? 'N/A') . "\n";
        echo "  First Name: " . ($self->first_name ?? 'N/A') . "\n";
        echo "  Last Name: " . ($self->last_name ?? 'N/A') . "\n";
        echo "  Username: @" . ($self->username ?? 'N/A') . "\n";
        echo "  Phone: " . ($self->phone ?? 'N/A') . "\n";
    } else {
        echo "Not logged in yet.\n";
    }

    echo "\n=== Test completed successfully! ===\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

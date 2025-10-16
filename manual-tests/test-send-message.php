#!/usr/bin/env php
<?php

/**
 * Send Test Message Script
 *
 * This script allows you to quickly send test messages to Telegram
 * without running the full test suite.
 *
 * Usage:
 *   php test-send-message.php "Your message here"
 *   php test-send-message.php "Message" --chat=me
 *   php test-send-message.php "Message" --chat=123456789
 */

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
} else {
    die("Error: .env file not found. Please copy .env.example to .env and configure it.\n");
}

// Parse command line arguments
$message = $argv[1] ?? null;
$chatId = $_ENV['MP_TEST_CHAT_ID'] ?? 'me';

foreach ($argv as $arg) {
    if (strpos($arg, '--chat=') === 0) {
        $chatId = substr($arg, 7);
    }
}

if (!$message) {
    echo "Usage: php test-send-message.php \"Your message here\" [--chat=CHAT_ID]\n";
    echo "\nExamples:\n";
    echo "  php test-send-message.php \"Hello World\"\n";
    echo "  php test-send-message.php \"Hello\" --chat=me\n";
    echo "  php test-send-message.php \"Hello\" --chat=123456789\n";
    echo "\nDefault chat: $chatId\n";
    exit(1);
}

// Check credentials
$apiId = $_ENV['MP_TELEGRAM_API_ID'] ?? null;
$apiHash = $_ENV['MP_TELEGRAM_API_HASH'] ?? null;

if (!$apiId || !$apiHash || $apiId === 'your_api_id_here') {
    die("Error: Please configure MP_TELEGRAM_API_ID and MP_TELEGRAM_API_HASH in .env file.\n");
}

$sessionFile = __DIR__ . '/../storage/app/telegram/' . ($_ENV['MP_SESSION_FILE'] ?? 'session.madeline');

if (!file_exists($sessionFile)) {
    die("Error: No authenticated session found. Please run: php test-telegram.php first\n");
}

echo "=== Send Test Message ===\n\n";
echo "Chat: $chatId\n";
echo "Message: $message\n\n";

try {
    // Create MadelineProto instance
    $settings = new \danog\MadelineProto\Settings();
    $settings->getAppInfo()
        ->setApiId((int)$apiId)
        ->setApiHash($apiHash);

    // Use default logger (ECHO_LOGGER for CLI, FILE_LOGGER otherwise)
    // Set to ERROR level to minimize output
    $settings->getLogger()
        ->setLevel(\danog\MadelineProto\Logger::LEVEL_ERROR);

    echo "Connecting to Telegram...\n";
    $client = new \danog\MadelineProto\API($sessionFile, $settings);
    $wrapper = new \PHPCore\MadelineProto\MadelineProto($client);

    // For numeric chat IDs, we need to fetch dialogs first to populate the peer database
    if (is_numeric($chatId) || (is_string($chatId) && preg_match('/^-?\d+$/', $chatId))) {
        echo "Fetching dialogs to populate peer database...\n";
        try {
            // Fetch recent dialogs to populate the internal peer database
            $client->messages->getDialogs([
                'offset_date' => 0,
                'offset_id' => 0,
                'offset_peer' => ['_' => 'inputPeerEmpty'],
                'limit' => 100,
                'hash' => 0,
            ]);
            echo "Dialogs fetched successfully.\n";
        } catch (\Exception $e) {
            echo "Warning: Could not fetch dialogs: " . $e->getMessage() . "\n";
        }
    }

    echo "Sending message...\n";
    $result = $wrapper->messages()->sendMessage($chatId, $message);

    echo "\n✓ Message sent successfully!\n\n";

    // Show result info
    if (isset($result->toArray()['_'])) {
        echo "Response type: " . $result->toArray()['_'] . "\n";
    }

    echo "\n=== Completed successfully! ===\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

#!/usr/bin/env php
<?php

/**
 * List Chats Script
 *
 * This script lists all available chats/dialogs that the authenticated
 * user can send messages to.
 *
 * Usage:
 *   php list-chats.php
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

echo "=== List Available Chats ===\n\n";

try {
    // Create MadelineProto instance
    $settings = new \danog\MadelineProto\Settings();
    $settings->getAppInfo()
        ->setApiId((int)$apiId)
        ->setApiHash($apiHash);

    // Set to ERROR level to minimize output
    $settings->getLogger()
        ->setLevel(\danog\MadelineProto\Logger::LEVEL_ERROR);

    echo "Connecting to Telegram...\n";
    $client = new \danog\MadelineProto\API($sessionFile, $settings);

    echo "Fetching dialogs...\n\n";
    // Get all dialogs using getFullDialogs - this returns an array with peer IDs as keys
    $fullDialogs = $client->getFullDialogs();

    echo "Available chats:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-20s %-40s %-15s\n", "Chat ID", "Name", "Type");
    echo str_repeat("-", 80) . "\n";

    $count = 0;
    foreach ($fullDialogs as $peerId => $dialog) {
        try {
            // Get info for each peer using the peer ID as key
            $info = $client->getInfo($peerId);

            $name = 'Unknown';
            $type = 'Unknown';

            // Extract name and type
            if (isset($info['User'])) {
                $user = $info['User'];
                $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                $type = isset($user['bot']) && $user['bot'] ? 'Bot' : 'User';
            } elseif (isset($info['Chat'])) {
                $chat = $info['Chat'];
                $name = $chat['title'] ?? 'Unknown';
                $type = 'Group';
            } elseif (isset($info['Channel'])) {
                $channel = $info['Channel'];
                $name = $channel['title'] ?? 'Unknown';
                $type = isset($channel['broadcast']) && $channel['broadcast'] ? 'Channel' : 'Supergroup';
            }

            // Truncate name if too long
            if (strlen($name) > 38) {
                $name = substr($name, 0, 35) . '...';
            }

            // Use the bot API ID format
            $botApiId = $info['bot_api_id'] ?? $peerId;

            printf("%-20s %-40s %-15s\n", $botApiId, $name, $type);
            $count++;

        } catch (\Exception $e) {
            // Skip peers that can't be resolved
            continue;
        }
    }

    echo str_repeat("-", 80) . "\n";
    echo "\nTotal: $count chats\n";
    echo "\nYou can use any of these Chat IDs with the test-send-message.php script.\n";
    echo "Example: php test-send-message.php \"Hello\" --chat=CHAT_ID\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

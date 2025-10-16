<?php

use PHPCore\MadelineProto\ClientMessages;
use PHPCore\MadelineProto\MadelineProto;
use PHPCore\MadelineProto\TelegramObject;

/**
 * Integration tests for sending messages to Telegram.
 *
 * These tests require a valid Telegram session and MP_TEST_CHAT_ID in .env
 *
 * To run these tests:
 * 1. Copy .env.example to .env
 * 2. Configure your Telegram credentials
 * 3. Run: php test-telegram.php (to authenticate)
 * 4. Set MP_TEST_CHAT_ID to a chat ID or "me" for Saved Messages
 * 5. Run: MP_INTEGRATION_TESTS=true vendor/bin/pest tests/Integration
 */

beforeEach(function () {
    // Skip if not running integration tests
    if (!getenv('MP_INTEGRATION_TESTS')) {
        $this->markTestSkipped('Integration tests are disabled. Set MP_INTEGRATION_TESTS=true to enable.');
    }

    // Load .env file
    if (file_exists(__DIR__ . '/../../.env')) {
        $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            putenv(trim($name) . '=' . trim($value));
        }
    }

    $apiId = getenv('MP_TELEGRAM_API_ID');
    $apiHash = getenv('MP_TELEGRAM_API_HASH');
    $this->testChatId = getenv('MP_TEST_CHAT_ID') ?: 'me';

    if (!$apiId || !$apiHash || $apiId === 'your_api_id_here') {
        $this->markTestSkipped('Telegram credentials not configured in .env');
    }

    // Create MadelineProto instance
    $sessionFile = __DIR__ . '/../../storage/app/telegram/' . (getenv('MP_SESSION_FILE') ?: 'test_session.madeline');

    if (!file_exists($sessionFile)) {
        $this->markTestSkipped('No authenticated session found. Run: php test-telegram.php first');
    }

    $settings = new \danog\MadelineProto\Settings();
    $settings->getAppInfo()
        ->setApiId((int)$apiId)
        ->setApiHash($apiHash);

    $settings->getLogger()
        ->setLevel(\danog\MadelineProto\Logger::LEVEL_ERROR);

    $this->client = new \danog\MadelineProto\API($sessionFile, $settings);
    $this->madeline = new MadelineProto($this->client);
    $this->messages = $this->madeline->messages();
});

test('can send a simple text message', function () {
    $result = $this->messages->sendMessage($this->testChatId, 'Hello from Pest test! 🧪');

    expect($result)->toBeInstanceOf(TelegramObject::class)
        ->and($result->toArray())->toBeArray()
        ->and($result->toArray())->toHaveKey('_');
})->group('integration', 'messages');

test('can send a message with markdown formatting', function () {
    $message = "**Bold text**\n_Italic text_\n`Code block`";

    $result = $this->messages->sendMessage(
        $this->testChatId,
        $message,
        ['parse_mode' => 'Markdown']
    );

    expect($result)->toBeInstanceOf(TelegramObject::class);
})->group('integration', 'messages');

test('can send a message with HTML formatting', function () {
    $message = "<b>Bold text</b>\n<i>Italic text</i>\n<code>Code block</code>";

    $result = $this->messages->sendMessage(
        $this->testChatId,
        $message,
        ['parse_mode' => 'HTML']
    );

    expect($result)->toBeInstanceOf(TelegramObject::class);
})->group('integration', 'messages');

test('can send multiple messages in sequence', function () {
    $messages = [
        'First message',
        'Second message',
        'Third message'
    ];

    foreach ($messages as $index => $message) {
        $result = $this->messages->sendMessage($this->testChatId, $message);

        expect($result)->toBeInstanceOf(TelegramObject::class);

        // Small delay between messages
        if ($index < count($messages) - 1) {
            usleep(500000); // 500ms
        }
    }

    expect(true)->toBeTrue();
})->group('integration', 'messages');

test('can send a silent message', function () {
    $result = $this->messages->sendMessage(
        $this->testChatId,
        'This is a silent message (no notification)',
        ['silent' => true]
    );

    expect($result)->toBeInstanceOf(TelegramObject::class);
})->group('integration', 'messages');

test('can check if logged in', function () {
    $isLoggedIn = $this->madeline->isLoggedIn();

    expect($isLoggedIn)->toBeTrue();
})->group('integration', 'auth');

test('can get self information', function () {
    $self = $this->madeline->getSelf();

    expect($self)->toBeInstanceOf(TelegramObject::class)
        ->and($self->id)->not->toBeNull()
        ->and($self->first_name)->not->toBeNull();
})->group('integration', 'auth');

test('messages instance is of correct type', function () {
    expect($this->messages)->toBeInstanceOf(ClientMessages::class);
})->group('integration', 'messages');

test('can access raw client', function () {
    $client = $this->madeline->getClient();

    expect($client)->toBeInstanceOf(\danog\MadelineProto\API::class);
})->group('integration', 'client');

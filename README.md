# Laravel MadelineProto
[![Latest Stable Version](https://poser.pugx.org/php-core/laravel-madeline-proto/v)](//packagist.org/packages/php-core/laravel-madeline-proto)
[![Total Downloads](https://poser.pugx.org/php-core/laravel-madeline-proto/downloads)](//packagist.org/packages/php-core/laravel-madeline-proto)
[![License](https://poser.pugx.org/php-core/laravel-madeline-proto/license)](//packagist.org/packages/php-core/laravel-madeline-proto)
[![Tests](https://github.com/php-core/laravel-madeline-proto/actions/workflows/tests.yml/badge.svg)](https://github.com/php-core/laravel-madeline-proto/actions/workflows/tests.yml)

A third party Telegram client library [danog/MadelineProto](https://github.com/danog/MadelineProto) wrapper for Laravel.
Updated to support latest MadelineProto and PHP 8.4 by PHPCore

# Getting Started

Add the laravel-madeline-proto to the project dependency:

```shell script
composer require php-core/laravel-madeline-proto
```

Then publish the `telegram.php` config file:

```shell script
php artisan vendor:publish --provider="PHPCore\MadelineProto\MadelineProtoServiceProvider"
```

Set up the Telegram API key by providing env variables:

```dotenv
MP_TELEGRAM_API_ID=... //your telegram api id here
MP_TELEGRAM_API_HASH=... //your telegram api hash here
```

This wrapper package supports for running both [single](https://github.com/php-core/laravel-madeline-proto/wiki/Single-Telegram-Account) / [multiple](https://github.com/php-core/laravel-madeline-proto/wiki/Multiple-Telegram-Account) telegram account.

## Dig Deeper

Please check [wiki](https://github.com/php-core/laravel-madeline-proto/wiki) for more details about laravel-madeline-proto usage

# Testing

## Automated Testing

This package uses Pest PHP for automated testing. To run the test suite:

```bash
composer test
```

For more details on automated testing, see [TESTING.md](TESTING.md).

## Manual Testing with Telegram

To test the package with actual Telegram API connections locally:

1. Copy `.env.example` to `.env` and add your credentials
2. Run the test script: `php test-telegram.php`
3. Send test messages: `php test-send-message.php "Hello!"`

For detailed instructions, see [MANUAL_TESTING.md](MANUAL_TESTING.md).

### Integration Tests

Run integration tests that interact with real Telegram API:

```bash
export MP_INTEGRATION_TESTS=true
vendor/bin/pest tests/Integration
```

These tests will send actual messages to the chat ID configured in `MP_TEST_CHAT_ID` (defaults to "me" for Saved Messages).

# Notes

* This wrapper package is still not wrapping all the apis yet, I'm still focusing on wrapping the messages api.

* If you can't find the method that you want in Messages facade or need to use the default danog/MadelineProto api, you might want to use `MadelineProto::getClient()` facade method. It will return `danog\MadelineProto\API` object where you can call all the method provided by the [danog/MadelineProto](https://github.com/danog/MadelineProto) library.

# Thanks To

[setiawanhu](https://github.com/setiawanhu) for the base version this package is based on

[Bryan Ramaputra](https://github.com/Ordinal43) for helping me to write readable documentations.  

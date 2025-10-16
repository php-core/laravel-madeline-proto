# Testing Guide

This package uses [Pest PHP](https://pestphp.com/) for testing. Pest is a delightful PHP testing framework with a focus on simplicity.

## Running Tests

Run all tests:

```bash
composer test
```

Run tests with coverage:

```bash
composer test:coverage
```

Run specific test file:

```bash
vendor/bin/pest tests/Unit/TelegramObjectTest.php
```

Run tests with filter:

```bash
vendor/bin/pest --filter="telegram object"
```

## Test Structure

Tests are organized into three main directories:

- **`tests/Unit/`** - Unit tests for individual classes and components
- **`tests/Feature/`** - Feature tests for Laravel integration and service provider
- **`tests/Integration/`** - Integration tests with actual Telegram API (requires authentication)

### Unit Tests

Unit tests cover:
- `TelegramObject` - Data object wrapper functionality
- `Constants` - Application constants
- `Exceptions` - Custom exception classes
- `Factory` - MadelineProto factory instantiation

### Feature Tests

Feature tests cover:
- Service Provider registration
- Configuration loading
- Laravel integration

### Integration Tests

Integration tests cover:
- Sending messages to Telegram
- Message formatting (Markdown, HTML)
- Authentication verification
- Client interaction

**Note:** Integration tests are disabled by default. To run them:
1. Authenticate first: `php test-telegram.php`
2. Configure `MP_TEST_CHAT_ID` in `.env`
3. Enable tests: `export MP_INTEGRATION_TESTS=true`
4. Run: `vendor/bin/pest tests/Integration`

See [MANUAL_TESTING.md](MANUAL_TESTING.md) for details.

## Writing Tests

### Basic Test

```php
<?php

test('can create telegram object', function () {
    $object = new TelegramObject(['name' => 'John']);

    expect($object->name)->toBe('John');
});
```

### Test with Setup

```php
<?php

beforeEach(function () {
    $this->data = ['id' => 123];
});

test('uses setup data', function () {
    expect($this->data['id'])->toBe(123);
});
```

### Testing Exceptions

```php
<?php

test('throws exception', function () {
    expect(fn() => someFunction())
        ->toThrow(CustomException::class);
});
```

## Test Configuration

The test suite uses:
- **Orchestra Testbench** - Laravel package testing
- **Mockery** - Mocking framework
- **PHPUnit 11** - Testing framework (via Pest)

Test configuration is in:
- [`phpunit.xml`](phpunit.xml) - PHPUnit configuration
- [`tests/Pest.php`](tests/Pest.php) - Pest configuration
- [`tests/TestCase.php`](tests/TestCase.php) - Base test case

## Continuous Integration

To add testing to your CI pipeline:

```yaml
# GitHub Actions example
- name: Run tests
  run: composer test
```

## Coverage Reports

Coverage reports are generated in the `build/` directory:
- `build/coverage/` - HTML coverage report
- `build/coverage.txt` - Text coverage report
- `build/logs/clover.xml` - Clover XML format

## Troubleshooting

### Tests fail to run

Make sure all dependencies are installed:
```bash
composer install
```

### Missing configuration

The test suite automatically configures the Laravel environment. No additional setup is needed.

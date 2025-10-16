# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Comprehensive Pest PHP testing framework integration
- Unit tests for TelegramObject, Constants, Exceptions, and Factory classes
- Feature tests for Service Provider and Laravel integration
- Integration tests for sending messages to Telegram
- Test helper scripts:
  - `test-telegram.php` - Authenticate and test connection
  - `test-send-message.php` - Send test messages from CLI
- Comprehensive testing documentation:
  - `TESTING.md` - Automated testing guide
  - `MANUAL_TESTING.md` - Manual and integration testing guide
- `.env.example` with all configuration options
- GitHub Actions workflow for automated testing
- Storage directory structure for sessions and logs

### Changed
- Updated to MadelineProto v8 Settings API (breaking change)
- Updated `MadelineProtoFactory` to use new Settings objects instead of arrays
- Updated config file with better documentation and logger level support
- Legacy array-based config is automatically converted to Settings objects

### Fixed
- Compatibility with MadelineProto v8+ final classes
- Settings configuration now uses proper Settings objects

## Configuration Migration

If you're upgrading from an older version, your existing config will continue to work.
The factory automatically converts array-based configuration to the new Settings objects.

### Old Config (still supported):
```php
'settings' => [
    'app_info' => ['api_id' => 123, 'api_hash' => 'abc'],
    'logger' => ['logger' => 2, 'logger_param' => 'path/to/log']
]
```

### New Config (recommended):
```php
'settings' => [
    'app_info' => ['api_id' => 123, 'api_hash' => 'abc'],
    'logger' => [
        'logger' => Logger::FILE_LOGGER,
        'logger_param' => 'path/to/log',
        'logger_level' => Logger::NOTICE
    ]
]
```

## Testing

### Run Unit Tests
```bash
composer test
```

### Run Integration Tests
```bash
export MP_INTEGRATION_TESTS=true
vendor/bin/pest tests/Integration
```

See [TESTING.md](TESTING.md) and [MANUAL_TESTING.md](MANUAL_TESTING.md) for details.

# Manual Testing Guide

This guide helps you test the package with actual Telegram API connections locally without committing your credentials.

## Setup

### 1. Get Telegram API Credentials

1. Go to https://my.telegram.org/apps
2. Log in with your phone number
3. Create a new application
4. Note your `api_id` and `api_hash`

### 2. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and add your credentials:

```env
MP_TELEGRAM_API_ID=12345678
MP_TELEGRAM_API_HASH=your_api_hash_here
MP_SESSION_FILE=test_session.madeline
MP_LOGGER_PATH=storage/logs/madeline_proto.log
```

**Important:** The `.env` file is gitignored and will never be committed.

### 3. Configure Test Chat (Optional)

For integration tests, set the chat ID where test messages will be sent:

```env
MP_TEST_CHAT_ID=me  # Use "me" for Saved Messages, or a numeric chat ID
```

### 4. Create Storage Directory

The package will create this automatically, but you can create it manually:

```bash
mkdir -p storage/app/telegram
mkdir -p storage/logs
```

## Running Tests

### Standalone Test Script

The package includes a standalone test script that doesn't require a full Laravel installation:

```bash
php test-telegram.php
```

This script will:
- Load your `.env` configuration
- Connect to Telegram
- Authenticate (if needed)
- Display your account information
- Save the session for future use

### First Run

On the first run, you'll be prompted to:
1. Enter your phone number (with country code, e.g., +1234567890)
2. Enter the verification code sent to your Telegram app
3. Enter your 2FA password (if enabled)

### Subsequent Runs

Once authenticated, the session is saved in `storage/app/telegram/test_session.madeline` and you won't need to re-authenticate unless the session expires.

### Send Test Messages

After authenticating, you can send test messages:

```bash
php test-send-message.php "Hello from CLI!"
```

With custom chat ID:

```bash
php test-send-message.php "Test message" --chat=me
php test-send-message.php "Hello" --chat=123456789
```

### Integration Tests

Run integration tests that send actual messages to Telegram:

```bash
# Enable integration tests
export MP_INTEGRATION_TESTS=true

# Run all integration tests
vendor/bin/pest tests/Integration

# Run only message tests
vendor/bin/pest tests/Integration --group=messages

# Run only auth tests
vendor/bin/pest tests/Integration --group=auth
```

**Note:** Integration tests require:
1. An authenticated session (run `php test-telegram.php` first)
2. `MP_TEST_CHAT_ID` configured in `.env`
3. `MP_INTEGRATION_TESTS=true` environment variable

Integration tests will:
- Send test messages to the configured chat
- Test various message formats (Markdown, HTML)
- Test message options (silent, reply markup)
- Verify authentication status

## Testing in a Laravel Application

If you want to test the package in an actual Laravel application:

### 1. Install the Package

```bash
composer require php-core/laravel-madeline-proto
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="PHPCore\MadelineProto\MadelineProtoServiceProvider"
```

### 3. Configure Laravel `.env`

Add to your Laravel `.env`:

```env
MP_TELEGRAM_API_ID=12345678
MP_TELEGRAM_API_HASH=your_api_hash_here
MP_SESSION_FILE=session.madeline
```

### 4. Test in Tinker

```bash
php artisan tinker
```

```php
// Using the facade
use PHPCore\MadelineProto\Facades\MadelineProto;

// Login
MadelineProto::phoneLogin('+1234567890');
// Check Telegram for code, then:
MadelineProto::completePhoneLogin('12345');

// Get account info
$self = MadelineProto::getSelf();
echo $self->first_name;

// Or get the raw client
$client = MadelineProto::getClient();
```

### 5. Test Commands

The package provides artisan commands:

```bash
# Login to a Telegram account
php artisan telegram:login

# Multi-session management
php artisan telegram:multi-session
```

## File Structure

```
.
├── .env                          # Your local config (gitignored)
├── .env.example                  # Template for credentials
├── test-telegram.php             # Standalone test script
├── storage/
│   ├── app/telegram/             # Session files (gitignored)
│   │   └── *.madeline            # MadelineProto sessions
│   └── logs/                     # Log files
│       └── madeline_proto.log    # MadelineProto logs
```

## Troubleshooting

### "API ID invalid"

Make sure your API ID is a number, not a string. In `.env`:
```env
MP_TELEGRAM_API_ID=12345678  # ✓ Correct
MP_TELEGRAM_API_ID="12345678"  # ✗ Wrong (remove quotes)
```

### "Session file not found"

The session file is created automatically on first login. If you see this error, ensure:
- The `storage/app/telegram/` directory exists
- The directory is writable (chmod 755)

### "Authorization required"

Your session may have expired. Delete the session file and re-authenticate:

```bash
rm storage/app/telegram/*.madeline*
php test-telegram.php
```

### Permission Errors

Ensure storage directories are writable:

```bash
chmod -R 755 storage/
```

## Security Notes

1. **Never commit `.env` files** - They contain sensitive credentials
2. **Never commit `.madeline` files** - They contain your authenticated session
3. **Keep your API credentials secret** - Don't share them publicly
4. **Use different sessions for testing** - Don't use your primary account's session in development

## Cleaning Up

To remove all test data:

```bash
# Remove sessions
rm -rf storage/app/telegram/*.madeline*

# Remove logs
rm -rf storage/logs/madeline_proto*.log

# Keep the .env for future testing (or remove it)
# rm .env
```

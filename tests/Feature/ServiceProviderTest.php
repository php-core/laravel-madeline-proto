<?php

use PHPCore\MadelineProto\ClientMessages;
use PHPCore\MadelineProto\Factories\MadelineProtoFactory;
use PHPCore\MadelineProto\MadelineProto;
use PHPCore\MadelineProto\MadelineProtoServiceProvider;

test('service provider is registered', function () {
    $providers = app()->getLoadedProviders();

    expect($providers)->toHaveKey(MadelineProtoServiceProvider::class);
});

test('madeline proto factory is bound', function () {
    expect(app()->bound('madeline-proto-factory'))->toBeTrue()
        ->and(app()->bound(MadelineProtoFactory::class))->toBeTrue();
});

test('telegram config is loaded', function () {
    expect(config('telegram'))->toBeArray()
        ->and(config('telegram.sessions'))->toBeArray()
        ->and(config('telegram.settings'))->toBeArray();
});

test('config has api credentials', function () {
    expect(config('telegram.settings.app_info.api_id'))->not->toBeNull()
        ->and(config('telegram.settings.app_info.api_hash'))->not->toBeNull();
});

test('config has logger settings', function () {
    expect(config('telegram.settings.logger'))->toBeArray()
        ->and(config('telegram.settings.logger.logger'))->not->toBeNull();
});

test('config has session settings', function () {
    expect(config('telegram.sessions.single.session_file'))->not->toBeNull()
        ->and(config('telegram.sessions.multiple.table'))->toBe('telegram_sessions');
});

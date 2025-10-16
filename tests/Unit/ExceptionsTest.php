<?php

use PHPCore\MadelineProto\Exceptions\NeedTwoFactorAuthException;
use PHPCore\MadelineProto\Exceptions\SignUpNeededException;
use PHPCore\MadelineProto\TelegramObject;

test('need two factor auth exception stores account', function () {
    $account = new TelegramObject(['id' => 123]);
    $exception = new NeedTwoFactorAuthException($account);

    expect($exception->account)->toBe($account)
        ->and($exception->getMessage())->toBe('User enabled 2FA, more steps needed')
        ->and($exception->getCode())->toBe(400);
});

test('need two factor auth exception can have custom message', function () {
    $account = new TelegramObject(['id' => 123]);
    $exception = new NeedTwoFactorAuthException($account, 'Custom message');

    expect($exception->getMessage())->toBe('Custom message');
});

test('sign up needed exception has default message', function () {
    $exception = new SignUpNeededException();

    expect($exception->getMessage())->toBe('Sign up needed')
        ->and($exception->getCode())->toBe(400);
});

test('sign up needed exception can have custom message', function () {
    $exception = new SignUpNeededException('Please sign up');

    expect($exception->getMessage())->toBe('Please sign up');
});

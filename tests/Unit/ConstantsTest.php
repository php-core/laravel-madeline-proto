<?php

use PHPCore\MadelineProto\Constants\Account;
use PHPCore\MadelineProto\Constants\Authorization;

test('account constants are defined correctly', function () {
    expect(Account::NEED_SIGN_UP)->toBe('account.needSignup')
        ->and(Account::PASSWORD)->toBe('account.password');
});

test('authorization constants are defined correctly', function () {
    expect(Authorization::NO_PASSWORD)->toBe('account.noPassword');
});

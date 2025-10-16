<?php

use PHPCore\MadelineProto\TelegramObject;

test('can create telegram object with array', function () {
    $data = ['name' => 'John', 'age' => 30];
    $object = new TelegramObject($data);

    expect($object->name)->toBe('John')
        ->and($object->age)->toBe(30);
});

test('can create telegram object without data', function () {
    $object = new TelegramObject();

    expect($object->name)->toBeNull();
});

test('can get return type from underscore field', function () {
    $data = ['_' => 'account.password'];
    $object = new TelegramObject($data);

    expect($object->return_type)->toBe('account.password');
});

test('can set properties dynamically', function () {
    $object = new TelegramObject();
    $object->username = 'testuser';

    expect($object->username)->toBe('testuser');
});

test('can check if property is set', function () {
    $object = new TelegramObject(['name' => 'John']);

    expect(isset($object->name))->toBeTrue()
        ->and(isset($object->email))->toBeFalse();
});

test('can convert to array', function () {
    $data = ['name' => 'John', 'age' => 30];
    $object = new TelegramObject($data);

    expect($object->toArray())->toBe($data);
});

test('can convert nested arrays to array', function () {
    $data = [
        'user' => [
            'name' => 'John',
            'address' => [
                'city' => 'New York',
                'zip' => '10001'
            ]
        ]
    ];
    $object = new TelegramObject($data);

    expect($object->toArray())->toBe($data);
});

test('can convert nested telegram objects to array', function () {
    $nested = new TelegramObject(['city' => 'New York']);
    $data = ['address' => $nested];
    $object = new TelegramObject($data);

    $result = $object->toArray();

    expect($result)->toHaveKey('address')
        ->and($result['address'])->toBe(['city' => 'New York']);
});

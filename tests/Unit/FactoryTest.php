<?php

use PHPCore\MadelineProto\Factories\MadelineProtoFactory;
use PHPCore\MadelineProto\MadelineProto;
use Illuminate\Database\DatabaseManager;

test('factory can be instantiated', function () {
    $db = Mockery::mock(DatabaseManager::class);
    $db->shouldReceive('connection')->andReturnSelf();

    $factory = new MadelineProtoFactory($db, 'telegram_sessions');

    expect($factory)->toBeInstanceOf(MadelineProtoFactory::class);
});

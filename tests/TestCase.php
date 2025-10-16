<?php

namespace PHPCore\MadelineProto\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use PHPCore\MadelineProto\MadelineProtoServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            MadelineProtoServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup default test environment
        $app['config']->set('telegram.sessions.single.session_file', 'test_session.madeline');
        $app['config']->set('telegram.sessions.multiple.table', 'telegram_sessions');
        $app['config']->set('telegram.settings.app_info.api_id', 'test_api_id');
        $app['config']->set('telegram.settings.app_info.api_hash', 'test_api_hash');
        $app['config']->set('telegram.settings.logger.logger', 1);
        $app['config']->set('telegram.settings.logger.logger_param', 'test.log');
    }
}

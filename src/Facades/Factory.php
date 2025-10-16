<?php

namespace PHPCore\MadelineProto\Facades;

use Illuminate\Support\Facades\Facade;
use \PHPCore\MadelineProto\MadelineProto;

/**
 * Facade for MadelineProtoFactory class.
 *
 * @package PHPCore\MadelineProto\Facades
 *
 * @method static MadelineProto get(mixed $session, array $config = null)
 * @method static MadelineProto make(string $sessionFile, array $config)
 */
class Factory extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return 'madeline-proto-factory';
    }
}

<?php

use danog\MadelineProto\Logger;

return [

    /*
    |--------------------------------------------------------------------------
    | Madeline Proto Sessions
    |--------------------------------------------------------------------------
    |
    | To store information about an account session and avoid re-logging in, serialization must be done.
    | A MadelineProto session is automatically serialized every
    | settings['serialization']['serialization_interval'] seconds (by default 30 seconds),
    | and on shutdown. If the scripts shutdowns normally (without ctrl+c or fatal errors/exceptions), the
    | session will also be serialized automatically.
    |
    | Types: "single", "multiple"
    |
    */

    'sessions' => [

        'single' => [
            'session_file' => env('MP_SESSION_FILE', 'session.madeline'),
        ],

        'multiple' => [
            'table' => 'telegram_sessions'
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Madeline Proto Settings
    |--------------------------------------------------------------------------
    |
    | Settings for MadelineProto. These are converted internally to MadelineProto's
    | Settings object for compatibility with MadelineProto v8+.
    |
    | For more configuration options, see:
    | https://docs.madelineproto.xyz/docs/SETTINGS.html
    |
    */

    'settings' => [

        'logger' => [
            // Logger type: FILE_LOGGER (2), STDOUT_LOGGER (1), NO_LOGGER (0)
            'logger' => Logger::FILE_LOGGER,

            // Logger output path (for FILE_LOGGER)
            'logger_param' => env('MP_LOGGER_PATH', storage_path('logs/madeline_proto_' . date('dmY') . '.log')),

            // Logger level: ULTRA_VERBOSE (5), VERBOSE (4), NOTICE (3), WARNING (2), ERROR (1), FATAL_ERROR (0)
            'logger_level' => env('MP_LOGGER_LEVEL', Logger::NOTICE),
        ],

        'app_info' => [
            // Get these from https://my.telegram.org/apps
            'api_id' => env('MP_TELEGRAM_API_ID', ''),

            'api_hash' => env('MP_TELEGRAM_API_HASH', ''),
        ],
    ],
];

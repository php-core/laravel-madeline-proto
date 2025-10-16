<?php

namespace PHPCore\MadelineProto\Factories;

use danog\MadelineProto\API;
use PHPCore\MadelineProto\MadelineProto;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;

class MadelineProtoFactory
{
    /**
     * @var Connection
     */
    private $database;

    /**
     * Table name.
     *
     * @var string
     */
    private $table;

    /**
     * SessionFactory constructor.
     *
     * @param DatabaseManager $manager
     * @param string $table
     */
    public function __construct(DatabaseManager $manager, string $table)
    {
        $this->database = $manager->connection();
        $this->table = $table;
    }

    /**
     * Get the MadelineProto (session) instance from session table.
     *
     * @param int|Model $session can be either <b>id</b> or model instance of <b>TelegramSession</b> which
     *                           generated from <u>madeline-proto:multi-session --model</u> command
     * @param array|null $config if this parameter is null, then the config from <b>telegram.php</b>
     *                           file will be used
     * @return MadelineProto
     */
    public function get($session, array $config = null)
    {
        if (is_int($session)) {
            $session = $this->database->table($this->table)->find($session);

            $sessionFile = $session->session_file;
        } else {
            $sessionFile = $session->session_file;
        }

        return $this->make($sessionFile, $config);
    }

    /**
     * Generating MadelineProto (session) instance.
     *
     * @param string $sessionFile
     * @param array|null $config if this parameter is null, then the config from <b>telegram.php</b>
     *                           file will be used
     * @return MadelineProto
     */
    public function make(string $sessionFile, array $config = null)
    {
        if (is_null($config)) {
            $config = config('telegram.settings');
        }

        // Convert old array config to new Settings object
        $settings = $this->convertConfigToSettings($config);

        $client = new API(storage_path("app/telegram/$sessionFile"), $settings);

        return new MadelineProto($client);
    }

    /**
     * Convert legacy array config to MadelineProto Settings object.
     *
     * @param array $config
     * @return \danog\MadelineProto\Settings
     */
    private function convertConfigToSettings(array $config): \danog\MadelineProto\Settings
    {
        $settings = new \danog\MadelineProto\Settings();

        // App Info
        if (isset($config['app_info'])) {
            $appInfo = $settings->getAppInfo();
            if (isset($config['app_info']['api_id'])) {
                $appInfo->setApiId((int)$config['app_info']['api_id']);
            }
            if (isset($config['app_info']['api_hash'])) {
                $appInfo->setApiHash($config['app_info']['api_hash']);
            }
        }

        // Logger
        if (isset($config['logger'])) {
            $logger = $settings->getLogger();
            if (isset($config['logger']['logger'])) {
                $logger->setType($config['logger']['logger']);
            }
            if (isset($config['logger']['logger_param'])) {
                $logger->setExtra($config['logger']['logger_param']);
            }
            if (isset($config['logger']['logger_level'])) {
                $logger->setLevel($config['logger']['logger_level']);
            }
        }

        return $settings;
    }
}

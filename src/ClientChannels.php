<?php

namespace PHPCore\MadelineProto;

use PHPCore\MadelineProto\TelegramObject;
use danog\MadelineProto\channels;
use danog\MadelineProto\API;
use danog\MadelineProto\File\FileReference;

class ClientChannels
{
    /**
     * @var channels
     */
    private $channels;

    /**
     * @var API
     */
    private $madelineProto;

    /**
     * ClientChannels constructor.
     *
     * @param API $madelineProto
     */
    public function __construct(API $madelineProto)
    {
        $this->channels = $madelineProto->channels;
        $this->madelineProto = $madelineProto;
    }

    /**
     * Deletes a channel/supergroup.
     *
     * For convenience, you may pass a {@link \PHPCore\MadelineProto\TelegramObject TelegramObject} to the first argument which contains
     * <strong>channels.deleteChannel</strong> method payload. It's fields will be sent as payload.
     *
     * @param mixed $channel
     * @return TelegramObject Updates
     */
    public function deleteChannel($channel): TelegramObject
    {
        if ($channel instanceof TelegramObject) {
            $channel = $channel->toArray();
        }

        return new TelegramObject($this->channels->deleteChannel(channel: $channel));
    }

    /**
     * Edits the photo of a channel/supergroup.
     *
     * @param mixed $channel The channel to edit.
     * @param mixed $photo The new photo (must be an InputChatPhoto or one of its constructors).
     * @return TelegramObject Updates
     */
    public function editPhoto(mixed $channel, mixed $photo): TelegramObject
    {
        if ($photo instanceof TelegramObject) {
            $photo = $photo->toArray();
        } else if (is_string($channel) || is_numeric($channel)) {
            $channel = $this->getInfo($channel);
        }

        return new TelegramObject($this->channels->editPhoto(channel: $channel, photo: $photo));
    }

    /**
     * Sets the photo of a channel/supergroup from a local file path.
     *
     * @param mixed $channel The channel to edit.
     * @param string $filePath The local file path to the photo.
     * @return TelegramObject Updates
     * @throws \Exception
     */
    public function setPhotoFromLocalFile(mixed $channel, string $filePath): TelegramObject
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('File not found: ' . $filePath);
        }

        $inputFile = $this->madelineProto->upload($filePath);

        return $this->editPhoto($channel, ['_' => 'inputChatUploadedPhoto', 'file' => $inputFile]);
    }

    /**
     * Get Client channels instance.
     *
     * @return channels channels APIFactory.
     */
    public function getChannels(): channels
    {
        return $this->channels;
    }

    /**
     * Creates a channel/supergroup.
     *
     * @param string $title Channel name
     * @param bool $broadcast Whether to create a channel or a supergroup
     * @param string|null $about About text for the channel
     * @param array $flags Flags, specify whether or not the channel is created as a forum
     * @param bool|null $for_import Whether to create the channel for import
     * @param string|null $address Geogroup address
     * @return TelegramObject Updates
     */
    public function createChannel(string $title, bool $broadcast, ?string $about = null, array $flags = [], ?bool $for_import = null, ?string $address = null): TelegramObject
    {
        $params = [
            'title' => $title,
            'broadcast' => $broadcast,
            'megagroup' => !$broadcast,
        ];

        if ($about !== null) {
            $params['about'] = $about;
        }

        if (!empty($flags)) {
            $params['flags'] = $flags;
        }

        if ($for_import !== null) {
            $params['for_import'] = $for_import;
        }

        if ($address !== null) {
            $params['address'] = $address;
        }

        return new TelegramObject($this->channels->createChannel(...$params));
    }

    /**
     * Edits the title of a channel/supergroup.
     *
     * @param mixed $channel The channel to edit.
     * @param string $title The new title.
     * @return TelegramObject Updates
     */
    public function editTitle(mixed $channel, string $title): TelegramObject
    {
        if ($channel instanceof TelegramObject) {
            $channel = $channel->toArray();
        } else if (is_string($channel) || is_numeric($channel)) {
            $channel = $this->getInfo($channel);
        }

        return new TelegramObject($this->channels->editTitle(channel: $channel, title: $title));
    }

    /**
     * Adds a user to a channel/supergroup.
     *
     * @param mixed $channel The channel to add the user to.
     * @param mixed $user The user to add.
     * @param int $fwd_limit
     * @return TelegramObject Updates
     */
    public function inviteToChannel(mixed $channel, mixed $user, int $fwd_limit = 100): TelegramObject
    {
        if ($channel instanceof TelegramObject) {
            $channel = $channel->toArray();
        } else if (is_string($channel) || is_numeric($channel)) {
            $channel = $this->getInfo($channel);
        }

        if ($user instanceof TelegramObject) {
            $user = $user->toArray();
        }

        return new TelegramObject($this->channels->inviteToChannel(channel: $channel, users: [$user], fwd_limit: $fwd_limit));
    }
}

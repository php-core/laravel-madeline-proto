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

    /**
     * Makes a user an admin in a channel/supergroup.
     *
     * @param mixed $channel The channel to make the user an admin in.
     * @param mixed $user The user to make an admin.
     * @param array $params Other parameters for the admin rights.
     * @return TelegramObject Updates
     */
    public function editAdmin(mixed $channel, mixed $user, array $params = [], string $rank = ''): TelegramObject
    {
        if ($channel instanceof TelegramObject) {
            $channel = $channel->toArray();
        } else if (is_string($channel) || is_numeric($channel)) {
            $channel = $this->getInfo($channel);
        }

        if ($user instanceof TelegramObject) {
            $user = $user->toArray();
        }

        $defaultParams = [
            'can_edit' => true,
            'can_post' => true,
            'can_invite' => true,
            'can_promote' => true,
            'can_change_info' => true,
            'can_delete_messages' => true,
            'can_pin_messages' => true,
            'can_manage_call' => true,
            'can_restrict_members' => true,
            'can_post_stories' => true,
            'can_edit_stories' => true,
            'can_delete_stories' => true,
            'anonymous' => true,
        ];

        $params = array_merge($defaultParams, $params);

        $adminRights = ['_' => 'chatAdminRights',
            'change_info' => $params['can_change_info'],
            'post_messages' => $params['can_post'],
            'edit_messages' => $params['can_edit'],
            'delete_messages' => $params['can_delete_messages'],
            'ban_users' => $params['can_restrict_members'],
            'invite_users' => $params['can_invite'],
            'pin_messages' => $params['can_pin_messages'],
            'manage_call' => $params['can_manage_call'],
            'post_stories' => $params['can_post_stories'],
            'edit_stories' => $params['can_edit_stories'],
            'delete_stories' => $params['can_delete_stories'],
            'add_admins' => $params['can_promote'],
            'anonymous' => $params['anonymous'] ?? false,
        ];

        $newParams = [
            'channel' => $channel,
            'user_id' => $user,
            'admin_rights' => $adminRights,
            'rank' => $rank
        ];

        return new TelegramObject($this->channels->editAdmin(...$newParams));
    }
}

<?php

namespace App\Provider;

use App\Exception\NotificationNotExistsException;

class NotificationServiceProvider
{
    public function __construct(private $defaultChannel, private $channels)
    {}

    /**
     * Get the list of available notification channels.
     *
     * This method retrieves an array of available notification channels.
     * If a default channel is specified, it is moved to the beginning of the list.
     *
     * @return array An array of notification channel names.
     */
    public function getChannels(): array
    {
        $defaultChannel = $this->defaultChannel;
        $channelNames = array_keys($this->channels);

        if ($defaultChannel) {
            $key = array_search($defaultChannel, $channelNames);

            if ($key !== false) {
                array_splice($channelNames, $key, 1);
                array_unshift($channelNames, $defaultChannel);
            }
        }

        return $channelNames;
    }

    /**
     * Get the list of notification providers for a specific channel.
     *
     * This method retrieves an array of notification providers associated with a given notification channel.
     *
     * @param string $channel The name of the notification channel.
     *
     * @return array An array of notification providers for the specified channel.
     *
     * @throws NotificationNotExistsException If the specified channel does not exist in the configuration.
     */
    public function getProvidersByChannel(string $channel): array
    {
        if (!array_key_exists($channel, $this->channels)) throw new NotificationNotExistsException('Invalid notification channel');

        return $this->channels[$channel] ?? [];
    }
}

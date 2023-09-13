<?php

namespace App\Tests\Unit;

use App\Exception\NotificationNotExistsException;
use App\Provider\NotificationServiceProvider;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class NotificationServiceProviderTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Boot the Symfony kernel for the test environment
        self::bootKernel();
    }

    public function testGetProvidersByChannel()
    {
        // Retrieve the Symfony Container
        $container = self::getContainer();

        // Retrieve the service with the mocked configuration
        $serviceProvider = $container->get(NotificationServiceProvider::class);
        $providers = $serviceProvider->getProvidersByChannel('email');

        $this->assertIsArray($providers);
    }

    public function testGetProvidersByNonExistingChannel()
    {
        // Retrieve the service with the mocked configuration
        $serviceProvider = new NotificationServiceProvider(null, ['sms', 'email']);

        // Use PHPUnit's expectException to check for the expected exception
        $this->expectException(NotificationNotExistsException::class);

        // Call the method for a non-existing channel
        $serviceProvider->getProvidersByChannel('random');
    }


    public function testGetChannelsWithDefaultChannel()
    {
        // Retrieve the service with the mocked configuration
        $container = self::getContainer();
        $serviceProvider = $container->get(NotificationServiceProvider::class);

        // Call the getChannels method
        $channels = $serviceProvider->getChannels();

        // Assert that the returned value is an array containing the default channel
        $this->assertIsArray($channels);
        $this->assertCount(2, $channels);
        $this->assertSame(['email', 'sms'], $channels);
    }

    public function testGetChannelsWithoutDefaultChannel()
    {
        // Create an instance of NotificationServiceProvider without a default channel
        $serviceProvider = new NotificationServiceProvider(null, []);

        // Call the getChannels method
        $channels = $serviceProvider->getChannels();

        // Assert that the returned value is an array containing all channel names
        $this->assertIsArray($channels);
        $this->assertCount(0, $channels);
    }
}

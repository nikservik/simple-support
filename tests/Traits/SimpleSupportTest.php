<?php

namespace Nikservik\SimpleSupport\Tests\Traits;

use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class SimpleSupportTest extends TestCase
{
    public function testSupportMessagesEmpty()
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->supportMessages);
    }

    public function testSupportMessages()
    {
        $user = User::factory()->hasSupportMessages(3)->create();

        $this->assertCount(3, $user->supportMessages);
    }

    public function testCountUnreadEmpty()
    {
        $user = User::factory()->create();

        $this->assertEquals(0, $user->countUnread);
    }

    public function testCountUnread()
    {
        $user = User::factory()->has(
            SupportMessage::factory()->count(3)->fromSupport()
        )->create();

        $this->assertEquals(3, $user->countUnread);
    }

    public function testCountUnreadNotifications()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->assertEquals(2, $user->countUnread);
    }

    public function testCountUnreadReadedNotifications()
    {
        $user = User::factory()->create();
        $notification = SupportMessage::factory()->notification()->create();
        SupportMessage::factory()->notificationRead($notification)->for($user)->create();

        $this->assertEquals(0, $user->countUnread);
    }

    public function testCountUnreadFastEmpty()
    {
        $user = User::factory()->create();

        $this->assertEquals(0, $user->countUnreadFast);
    }

    public function testCountUnreadFast()
    {
        $user = User::factory()->has(
            SupportMessage::factory()->count(3)->fromSupport()
        )->create();

        $this->assertEquals(3, $user->countUnreadFast);
    }

    public function testCountUnreadFastNotifications()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->assertEquals(2, $user->countUnreadFast);
    }

    public function testCountUnreadFastReadedNotifications()
    {
        $user = User::factory()->create();
        $notification = SupportMessage::factory()->notification()->create();
        SupportMessage::factory()->notificationRead($notification)->for($user)->create();

        $this->assertEquals(0, $user->countUnreadFast);
    }

    public function testCountUnreadFastSpeed()
    {
        $user = User::factory()->has(
            SupportMessage::factory()->count(500)->mayBeNotification()
        )->create();

        $this->assertGreaterThan(0, $user->countUnreadFast);
    }
}

<?php

namespace Nikservik\SimpleSupport\Tests\Traits;

use Illuminate\Support\Facades\Config;
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

    public function testSupportMessagesWithoutNotifications()
    {
        $user = User::factory()->hasSupportMessages(3)->create();
        SupportMessage::factory()->notification()->create();

        $this->assertCount(3, $user->supportMessages);
    }

    public function testSupportMessagesWithoutNotificationReadMarks()
    {
        $user = User::factory()->hasSupportMessages(3)->create();
        $notification = SupportMessage::factory()->notification()->create();
        SupportMessage::factory()->notificationRead($notification)->for($user)->create();

        $this->assertCount(3, $user->supportMessages);
    }

    public function testCountUnreadEmpty()
    {
        Config::set('simple-support.unread-count', 'simple');
        $user = User::factory()->create();

        $this->assertEquals(0, $user->countUnread);
    }

    public function testCountUnread()
    {
        Config::set('simple-support.unread-count', 'simple');
        $user = User::factory()->has(
            SupportMessage::factory()->count(3)->fromSupport()
        )->create();

        $this->assertEquals(3, $user->countUnread);
    }

    public function testCountUnreadDontCountOwnMessages()
    {
        Config::set('simple-support.unread-count', 'simple');
        $user = User::factory()->has(
            SupportMessage::factory()->count(3)->fromUser()
        )->create();

        $this->assertEquals(0, $user->countUnread);
    }

    public function testCountUnreadNotifications()
    {
        Config::set('simple-support.unread-count', 'simple');
        $user = User::factory()->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->assertEquals(2, $user->countUnread);
    }

    public function testCountUnreadReadedNotifications()
    {
        Config::set('simple-support.unread-count', 'simple');
        $user = User::factory()->create();
        $notification = SupportMessage::factory()->notification()->create();
        SupportMessage::factory()->notificationRead($notification)->for($user)->create();

        $this->assertEquals(0, $user->countUnread);
    }

    public function testCountUnreadFastEmpty()
    {
        Config::set('simple-support.unread-count', 'fast');
        $user = User::factory()->create();

        $this->assertEquals(0, $user->countUnread);
    }

    public function testCountUnreadFast()
    {
        Config::set('simple-support.unread-count', 'fast');
        $user = User::factory()->has(
            SupportMessage::factory()->count(3)->fromSupport()
        )->create();

        $this->assertEquals(3, $user->countUnread);
    }

    public function testCountUnreadFastNotifications()
    {
        Config::set('simple-support.unread-count', 'fast');
        $user = User::factory()->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->assertEquals(2, $user->countUnread);
    }

    public function testCountUnreadFastReadedNotifications()
    {
        Config::set('simple-support.unread-count', 'fast');
        $user = User::factory()->create();
        $notification = SupportMessage::factory()->notification()->create();
        SupportMessage::factory()->notificationRead($notification)->for($user)->create();

        $this->assertEquals(0, $user->countUnread);
    }

    public function testCountUnreadFastSpeed()
    {
        Config::set('simple-support.unread-count', 'fast');
        $user = User::factory()->has(
            SupportMessage::factory()->count(500)->mayBeNotification()
        )->create();

        $this->assertGreaterThan(0, $user->countUnread);
    }
}

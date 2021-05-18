<?php

namespace Nikservik\SimpleSupport\Tests\Models;

use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class SupportMessageTest extends TestCase
{
    public function testReadMarkForNotification()
    {
        $user = User::factory()->create();
        $notification = SupportMessage::factory()->notification()->create();
        SupportMessage::factory()->notificationRead($notification)->for($user)->create();

        $this->assertCount(1, $notification->readMarks);
        $this->assertInstanceOf(SupportMessage::class, $notification->readMarks()->first());
    }

    public function testReadMarkForSupportMessage()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->for($user)->fromSupport()->create();

        $this->assertCount(0, $message->readMarks);
    }

    public function testReplyTo()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromSupport()->for($user)->create();
        $reply = SupportMessage::factory()->fromUser()->replyTo($message)->for($user)->create();

        $this->assertInstanceOf(SupportMessage::class, $reply->replyTo);
        $this->assertEquals($message->id, $reply->replyTo->id);
    }
}

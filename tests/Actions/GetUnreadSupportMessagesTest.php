<?php

namespace Nikservik\SimpleSupport\Tests\Actions;

use Nikservik\SimpleSupport\Actions\GetUnreadSupportMessages;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class GetUnreadSupportMessagesTest extends TestCase
{
    public function testHandleEmpty()
    {
        $user = User::factory()->create();

        $this->assertEquals(0, GetUnreadSupportMessages::run($user));
    }

    public function testHandle()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(5)->for($user)->fromSupport()->create();

        $this->assertEquals(5, GetUnreadSupportMessages::run($user));
    }

    public function test_zero_without_messages()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/support/unread')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('data', 0);
    }

    public function test_with_messages_and_notifications()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(5)->for($user)->fromSupport()->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->actingAs($user)
            ->getJson('/support/unread')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('data', 7);
    }
}

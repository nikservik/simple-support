<?php

namespace Nikservik\SimpleSupport\Tests\Actions;

use Illuminate\Support\Facades\Config;
use Nikservik\SimpleSupport\Actions\NotifyTelegram;
use Nikservik\SimpleSupport\Actions\PostMessageFromUser;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class PostMessageFromUserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        NotifyTelegram::mock()->shouldReceive('handle');
    }

    public function testHandle()
    {
        $user = User::factory()->create();

        PostMessageFromUser::run($user, 'test message');

        $this->assertCount(1, $user->supportMessages);
        $this->assertEquals('test message', $user->supportMessages[0]->message);
    }

    public function testHandleWithReply()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->notification()->create();

        $newMessage = PostMessageFromUser::run($user, 'test message', $message->id);

        $this->assertEquals('test message', $newMessage->message);
        $this->assertEquals($message->id, $newMessage->reply_to);
    }

    public function test_creates_message()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/support', [
                'message' => 'test message',
            ])
            ->assertOk()
            ->assertJsonPath('status', 201)
            ->assertJsonPath('message', 'Created');

        $this->assertCount(1, $user->supportMessages);
        $this->assertEquals('test message', $user->supportMessages[0]->message);
    }

    public function test_dont_create_with_empty_message()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/support', [
                'message' => '',
            ])
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertJsonPath('errors.message.0', 'simple-support.message.required');

        $this->assertCount(0, $user->supportMessages);
    }

    public function test_creates_message_with_reply_to()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromSupport()->for($user)->create();

        $this->actingAs($user)
            ->postJson('/support', [
                'message' => 'test message',
                'reply_to' => $message->id,
            ])
            ->assertOk()
            ->assertJsonPath('status', 201)
            ->assertJsonPath('message', 'Created');

        $this->assertCount(2, $user->supportMessages);
        $this->assertEquals('test message', $user->supportMessages[1]->message);
        $this->assertEquals($message->id, $user->supportMessages[1]->reply_to);
    }

    public function test_fails_with_bad_reply_to()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/support', [
                'message' => 'test message',
                'reply_to' => 4,
            ])
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertJsonPath('errors.reply_to.0', 'simple-support.reply_to.exists');

        $this->assertCount(0, $user->supportMessages);
    }

    public function test_dont_create_when_feature_disabled()
    {
        $user = User::factory()->create();
        Config::set('simple-support.features', []);

        $this->actingAs($user)
            ->postJson('/support', [
                'message' => 'test message',
            ])
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.users_cant_send_messages');

        $this->assertCount(0, $user->supportMessages);
    }
}

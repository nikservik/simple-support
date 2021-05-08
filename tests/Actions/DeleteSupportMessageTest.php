<?php

namespace Nikservik\SimpleSupport\Tests\Actions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Nikservik\SimpleSupport\Actions\DeleteSupportMessage;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class DeleteSupportMessageTest extends TestCase
{
    public function testHandle()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();

        DeleteSupportMessage::run($user, $message->id);

        $this->assertCount(0, $user->supportMessages);
    }

    public function testHandleUnexistant()
    {
        $user = User::factory()->create();
        $this->expectException(ModelNotFoundException::class);

        DeleteSupportMessage::run($user, 3);
    }

    public function test_deletes_message()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();

        $this->actingAs($user)
            ->deleteJson('/support/' . $message->id)
            ->assertOk()
            ->assertJsonPath('status', 202)
            ->assertJsonPath('message', 'Accepted');

        $this->assertCount(0, $user->supportMessages);
    }

    public function test_fails_on_unexistant_message()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson('/support/3')
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.message_not_found');
    }

    public function test_fails_on_message_from_support()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromSupport()->for($user)->create();

        $this->actingAs($user)
            ->deleteJson('/support/' . $message->id)
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.you_can_delete_only_own_messages');
    }

    public function test_fails_on_notification()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->notification()->create();

        $this->actingAs($user)
            ->deleteJson('/support/' . $message->id)
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.you_can_delete_only_own_messages');
    }

    public function test_fails_on_other_users_message()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($otherUser)->create();

        $this->actingAs($user)
            ->deleteJson('/support/' . $message->id)
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.you_can_delete_only_own_messages');
    }

    public function test_fails_when_feature_disabled()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();
        Config::set('simple-support.features', []);

        $this->actingAs($user)
            ->deleteJson('/support/' . $message->id)
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.users_cant_delete_messages');
    }
}

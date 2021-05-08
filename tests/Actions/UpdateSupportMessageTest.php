<?php

namespace Nikservik\SimpleSupport\Tests\Actions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Nikservik\SimpleSupport\Actions\UpdateSupportMessage;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class UpdateSupportMessageTest extends TestCase
{
    public function testHandle()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();

        UpdateSupportMessage::run($user, $message->id, 'edited');

        $this->assertEquals('edited', $user->supportMessages[0]->message);
    }

    public function testHandleUnexistant()
    {
        $user = User::factory()->create();
        $this->expectException(ModelNotFoundException::class);

        UpdateSupportMessage::run($user, 3, 'edited');
    }

    public function test_updates_message()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();

        $this->actingAs($user)
            ->patchJson('/support/' . $message->id, ['message' => 'edited'])
            ->assertOk()
            ->assertJsonPath('status', 202)
            ->assertJsonPath('message', 'Accepted');

        $this->assertEquals('edited', $user->supportMessages[0]->message);
    }

    public function test_fails_on_empty_message()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();

        $this->actingAs($user)
            ->patchJson('/support/' . $message->id, ['message' => ''])
            ->assertStatus(422)
            ->assertJsonPath('errors.message.0', 'simple-support.message.required');

        $this->assertNotEquals('', $user->supportMessages[0]->message);
    }

    public function test_fails_on_unexistant_message()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patchJson('/support/3', ['message' => 'edited'])
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.message_not_found');
    }

    public function test_fails_on_message_from_support()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromSupport()->for($user)->create();

        $this->actingAs($user)
            ->patchJson('/support/' . $message->id, ['message' => 'edited'])
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.you_can_change_only_own_messages');
    }

    public function test_fails_on_notification()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->notification()->create();

        $this->actingAs($user)
            ->patchJson('/support/' . $message->id, ['message' => 'edited'])
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.you_can_change_only_own_messages');
    }

    public function test_fails_on_other_users_message()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($otherUser)->create();

        $this->actingAs($user)
            ->patchJson('/support/' . $message->id, ['message' => 'edited'])
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.you_can_change_only_own_messages');
    }

    public function test_fails_when_feature_disabled()
    {
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();
        Config::set('simple-support.features', []);

        $this->actingAs($user)
            ->patchJson('/support/'.$message->id, ['message' => 'edited'])
            ->assertStatus(403)
            ->assertJsonPath('message', 'simple-support.users_cant_update_messages');
    }
}

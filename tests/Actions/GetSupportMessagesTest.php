<?php

namespace Nikservik\SimpleSupport\Tests\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Nikservik\SimpleSupport\Actions\GetSupportMessages;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class GetSupportMessagesTest extends TestCase
{
    public function testHandleEmpty()
    {
        $user = User::factory()->create();

        $this->assertCount(0, GetSupportMessages::run($user));
    }

    public function testHandle()
    {
        $user = User::factory()->hasSupportMessages(3)->create();

        $this->assertCount(3, GetSupportMessages::run($user));
    }

    public function testHandleBefore()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(3)->for($user)->create(['created_at' => Carbon::now()->addDay()]);
        SupportMessage::factory()->count(5)->for($user)->create(['created_at' => Carbon::now()->addMonth()]);

        $this->assertCount(3, GetSupportMessages::run($user, Carbon::now()->addMonth()->subDay()));
    }

    public function test_does_not_show_messages_prior_user_registration()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(3)->for($user)->create(['created_at' => Carbon::now()->subDay()]);
        SupportMessage::factory()->count(5)->for($user)->create(['created_at' => Carbon::now()->addDay()]);

        $this->assertCount(5, GetSupportMessages::run($user));
    }

    public function test_user_without_messages()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/support')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonCount(0, 'data');
    }

    public function test_user_with_messages()
    {
        $user = User::factory()->hasSupportMessages(3)->create();

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonPath('status', 200)
            ->assertJsonCount(3, 'data');
    }

    public function test_messages_before()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(3)->for($user)->create(['created_at' => Carbon::now()->addDay()]);
        SupportMessage::factory()->count(5)->for($user)->create(['created_at' => Carbon::now()->addMonth()]);

        $this->actingAs($user)
            ->getJson('/support?before=' . urlencode(Carbon::now()->addMonth()->subDay()))
            ->assertJsonCount(3, 'data');
    }

    public function test_messages_limited()
    {
        $user = User::factory()->hasSupportMessages(10)->create();
        Config::set('simple-support.messages-per-page', 5);

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonCount(5, 'data');
    }

    public function test_messages_limited_last()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(3)->for($user)->fromUser()->create(['created_at' => Carbon::now()->subMonth()]);
        SupportMessage::factory()->count(5)->for($user)->fromSupport()->create();
        Config::set('simple-support.messages-per-page', 5);

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.type', 'supportMessage');
    }

    public function test_get_messages_marks_them_read()
    {
        $user = User::factory()->has(
            SupportMessage::factory()->count(3)->fromSupport()
        )->create();

        $response = $this->actingAs($user)->getJson('/support');

        $response->assertJsonPath('unread', 0);
        $this->assertNotNull($response['data']['0']['read_at']);
    }

    public function test_get_messages_marks_as_read_only_from_support()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(3)->for($user)->fromUser()->create();
        SupportMessage::factory()->count(2)->for($user)->fromSupport()->create();

        $response = $this->actingAs($user)->getJson('/support');

        $this->assertNull($response['data']['0']['read_at']);
        $this->assertNotNull($response['data']['4']['read_at']);
    }

    public function test_get_messages_marks_them_read_only_received()
    {
        $user = User::factory()->has(
            SupportMessage::factory()->count(10)->fromSupport()
        )->create();
        Config::set('simple-support.messages-per-page', 5);

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonPath('unread', 5);
    }

    public function test_get_messages_marks_them_read_only_from_support()
    {
        $user = User::factory()->has(
            SupportMessage::factory()->count(5)->fromUser()
        )->create();

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonPath('unread', 0)
            ->assertJsonPath('data.0.read_at', null);
    }

    public function test_messages_includes_notifications()
    {
        $user = User::factory()->hasSupportMessages(5)->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonCount(7, 'data');
    }

    public function test_messages_dont_include_notification_reads()
    {
        $user = User::factory()->hasSupportMessages(5)->create();
        $notification = SupportMessage::factory()->notification()->create();
        SupportMessage::factory()->notificationRead($notification)->for($user)->create();

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonCount(6, 'data');
    }

    public function test_get_messages_counts_notifications_as_read()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonPath('unread', 0);
    }

    public function test_get_messages_dont_duplicate_read_marks()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(2)->notification()->create();

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonPath('unread', 0);

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonPath('unread', 0);
    }

    public function test_get_messages_limit_dont_mark_old_notification_as_read()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(2)->notification()->create();
        SupportMessage::factory()->count(3)->for($user)->fromUser()->create();
        SupportMessage::factory()->count(2)->for($user)->fromSupport()->create();
        Config::set('simple-support.messages-per-page', 5);

        $this->actingAs($user)
            ->getJson('/support')
            ->assertJsonPath('unread', 2);
    }

    public function test_get_messages_before_dont_mark_newer_notification_as_read()
    {
        $user = User::factory()->create();
        SupportMessage::factory()->count(3)->for($user)->create(['created_at' => Carbon::now()->addDay()]);
        SupportMessage::factory()->count(2)->notification()->create(['created_at' => Carbon::now()->addMonth()]);

        $this->actingAs($user)
            ->getJson('/support?before=' . Carbon::now()->addDays(2))
            ->assertJsonPath('unread', 2);
    }
}

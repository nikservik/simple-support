<?php

namespace Nikservik\SimpleSupport\Tests\Actions;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Nikservik\SimpleSupport\Actions\NotifyTelegram;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Tests\TestCase;
use Nikservik\SimpleSupport\Tests\User;

class NotifyTelegramTest extends TestCase
{
    protected function fakeHttpOk()
    {
        Http::fake(['*' => Http::response(File::get(__DIR__.'/responses/responseOk.json'), 200)]);
    }

    protected function fakeHttpError()
    {
        Http::fake(['*' => Http::response(File::get(__DIR__.'/responses/response400.json'), 400)]);
    }

    public function testHandle()
    {
        $this->fakeHttpOk();
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();

        NotifyTelegram::run($message);

        Http::assertSent(function (Request $request) {
            return Str::contains($request->url(), Config::get('simple-support.telegram.url'));
        });
    }

    public function testNotSentWithoutUser()
    {
        $this->fakeHttpOk();
        $message = SupportMessage::factory()->fromUser()->create();

        NotifyTelegram::run($message);

        Http::assertNothingSent();
    }

    public function testNotSentFromSupport()
    {
        $this->fakeHttpOk();
        $message = SupportMessage::factory()->fromSupport()->create();

        NotifyTelegram::run($message);

        Http::assertNothingSent();
    }

    public function testNotSentForNotification()
    {
        $this->fakeHttpOk();
        $message = SupportMessage::factory()->notification()->create();

        NotifyTelegram::run($message);

        Http::assertNothingSent();
    }

    public function testReceivedError()
    {
        $this->fakeHttpError();
        Log::shouldReceive('error')->times(2);
        $user = User::factory()->create();
        $message = SupportMessage::factory()->fromUser()->for($user)->create();

        NotifyTelegram::run($message);
    }
}

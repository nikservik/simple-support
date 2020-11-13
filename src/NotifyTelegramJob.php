<?php

namespace Nikservik\SimpleSupport;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Nikservik\SimpleSupport\Models\SupportMessage;

class NotifyTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    public function __construct(SupportMessage $message)
    {
        $this->message = $message;
    }

    public function handle()
    {
        $response = Http::post(config('simple-support.telegram.url').config('simple-support.telegram.token').'/sendMessage', [
                'chat_id' => config('simple-support.telegram.chat'),
                'parse_mode'=>'MarkdownV2',
                'text' => "\\-\\-\n*Сообщение в " . __('app.name') . "*\n\n"
                    . "_{$this->message->user->name} пишет_\n\n"
                    . $this->prepareMessage($this->message->message) ."\n\n"
                    . '[Перейти к диалогу](' . str_replace('://', '://admin.', config('app.url')) . '/support/dialog/' . $this->message->user->id . '#read)',
            ]);
        if (! $response['ok'])
            Log::debug($response);
    }

    protected function prepareMessage(string $message): string
    {
        return str_replace(
            ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'],
            $message
        );
    }
}

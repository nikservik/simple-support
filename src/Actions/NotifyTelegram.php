<?php


namespace Nikservik\SimpleSupport\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsJob;
use Lorisleiva\Actions\Concerns\AsObject;
use Nikservik\SimpleSupport\Models\SupportMessage;

class NotifyTelegram
{
    use AsObject;
    use AsJob;
    use AsFake;

    public function handle(SupportMessage $message): void
    {
        if (! in_array('send-notifications-to-telegram', Config::get('simple-support.features'))) {
            return;
        }
        if ($message->type !== 'userMessage' || ! $message->user) {
            return;
        }

        $message = "\\-\\-\n*Сообщение в " . $this->prepareMessage(__('app.name')) . "*\n\n"
                    . "_" . $this->prepareMessage($message->user->name) . " пишет_\n\n"
                    . $this->prepareMessage($message->message) ."\n\n"
                    . '[Перейти к диалогу](' . config('app.url') . '/support/dialog/' . $message->user->id . '#read)';

        $response = Http::post(Config::get('simple-support.telegram.url').Config::get('simple-support.telegram.token').'/sendMessage', [
                'chat_id' => Config::get('simple-support.telegram.chat'),
                'parse_mode' => 'MarkdownV2',
                'text' => $message,
            ]);

        if (! $response['ok']) {
            Log::error($response);
            Log::error('Message: ' . $message);
        }
    }

    protected function prepareMessage(string $message): string
    {
        return str_replace(
            ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            ["\\_", "\\*", "\\[", "\\]", "\\(", "\\)", "\\~", "\\`", "\\>", "\\#", "\\+", "\\-", "\\=", "\\|", "\\{", "\\}", "\\.", "\\!"],
            $message
        );
    }
}

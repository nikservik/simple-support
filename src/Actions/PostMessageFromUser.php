<?php


namespace Nikservik\SimpleSupport\Actions;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;
use Nikservik\SimpleSupport\Models\SupportMessage;

class PostMessageFromUser
{
    use AsObject;
    use AsController;

    public static function route(): void
    {
        Route::post(
            '/' . Config::get('simple-support.route') . '/',
            static::class
        );
    }

    public function handle(User $user, string $message, int $reply_to = null): SupportMessage
    {
        return $user->supportMessages()->save(
            new SupportMessage([
                'message' => $message,
                'type' => 'userMessage',
                'reply_to' => $reply_to,
            ])
        );
    }

    public function asController(ActionRequest $request)
    {
        $user = Auth::user();

        $message = $this->handle($user, $request->get('message'), $request->get('reply_to'));

        NotifyTelegram::dispatch($message);
    }

    public function jsonResponse(): JsonResponse
    {
        return response()->json([
            'status' => 201,
            'message' => 'Created',
        ]);
    }

    public function authorize(ActionRequest $request): Response
    {
        if (! in_array('user-can-send-message', Config::get('simple-support.features'))) {
            return Response::deny('simple-support.users_cant_send_messages');
        }

        return Response::allow();
    }

    public function rules(): array
    {
        return [
            'message' => ['required'],
            'reply_to' => ['exists:support_messages,id', ],
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'message.required' => 'simple-support.message.required',
            'reply_to.exists' => 'simple-support.reply_to.exists',
        ];
    }
}

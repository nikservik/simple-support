<?php


namespace Nikservik\SimpleSupport\Actions;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;
use Nikservik\SimpleSupport\Models\SupportMessage;

class UpdateSupportMessage
{
    use AsObject;
    use AsController;

    public static function route(): void
    {
        Route::patch(
            '/' . Config::get('simple-support.route') . '/{message}',
            static::class
        );
    }

    public function handle(User $user, int $messageId, string $message): void
    {
        SupportMessage::findOrFail($messageId)
            ->update(['message' => $message]);
    }

    public function asController(ActionRequest $request, int $message)
    {
        $this->handle($request->user(), $message, $request->get('message'));

        return response()->json([
            'status' => 202,
            'message' => 'Accepted',
        ]);
    }

    public function authorize(ActionRequest $request): Response
    {
        if (! in_array('user-can-delete-message', Config::get('simple-support.features'))) {
            return Response::deny('simple-support.users_cant_update_messages');
        }

        if (! $message = SupportMessage::find($request->route()->parameter('message'))) {
            return Response::deny('simple-support.message_not_found');
        }

        if ($message->user_id != $request->user()->id || $message->type !== 'userMessage') {
            return Response::deny('simple-support.you_can_change_only_own_messages');
        }

        return Response::allow();
    }

    public function rules(): array
    {
        return [
            'message' => ['required'],
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'message.required' => 'simple-support.message.required',
        ];
    }
}

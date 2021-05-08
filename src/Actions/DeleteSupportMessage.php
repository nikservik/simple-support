<?php


namespace Nikservik\SimpleSupport\Actions;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;
use Nikservik\SimpleSupport\Models\SupportMessage;

class DeleteSupportMessage
{
    use AsObject;
    use AsController;

    public static function route(): void
    {
        Route::delete(
            '/' . Config::get('simple-support.route') . '/{message}',
            static::class
        );
    }

    public function handle(User $user, int $messageId): void
    {
        SupportMessage::findOrFail($messageId)->delete();
    }

    public function asController(ActionRequest $request, int $message)
    {
        $user = Auth::user();

        $this->handle($user, $message);

        return response()->json([
            'status' => 202,
            'message' => 'Accepted',
        ]);
    }

    public function authorize(ActionRequest $request): Response
    {
        if (! in_array('user-can-delete-message', Config::get('simple-support.features'))) {
            return Response::deny('simple-support.users_cant_delete_messages');
        }

        if (! $message = SupportMessage::find($request->route()->parameter('message'))) {
            return Response::deny('simple-support.message_not_found');
        }

        if ($message->user_id != $request->user()->id || $message->type !== 'userMessage') {
            return Response::deny('simple-support.you_can_delete_only_own_messages');
        }

        return Response::allow();
    }
}

<?php


namespace Nikservik\SimpleSupport\Actions;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;

class GetUnreadSupportMessages
{
    use AsObject;
    use AsController;

    public static function route(): void
    {
        Route::get(
            '/' . Config::get('simple-support.route') . '/unread',
            static::class
        );
    }

    public function handle(User $user): int
    {
        $countUnread = Config::get('simple-support.unread-count') == 'fast'
            ? 'countUnreadFast'
            : 'countUnread';

        return $user->$countUnread;
    }

    public function asController(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'status' => 200,
            'data' => $this->handle($user),
        ]);
    }
}

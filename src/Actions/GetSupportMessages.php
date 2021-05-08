<?php


namespace Nikservik\SimpleSupport\Actions;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Models\SupportMessageResource;

class GetSupportMessages
{
    use AsObject;
    use AsController;

    public static function route(): void
    {
        Route::get(
            '/' . Config::get('simple-support.route') . '/',
            static::class
        );
    }

    public function handle(User $user, Carbon $before = null): Collection
    {
        $messages = SupportMessage::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                    ->orWhere(
                        fn ($query) =>
                        $query->whereNull('user_id')
                            ->where('type', 'notification')
                    );
        })
            ->where('type', '<>', 'notificationRead')
            ->limit(Config::get('simple-support.messages-per-page'), 10)
            ->orderBy('created_at', 'DESC');

        if ($before) {
            $messages->where('created_at', '<', $before);
        }

        $this->markAsRead($messages, $user);

        return $messages->get();
    }

    public function asController(Request $request)
    {
        $user = Auth::user();
        $before = $request->has('before')
            ? Carbon::parse($request->get('before'))
            : null;

        $countUnread = Config::get('simple-support.unread-count') == 'fast'
            ? 'countUnreadFast'
            : 'countUnread';

        return SupportMessageResource::collection(
            $this->handle($user, $before)
        )
            ->additional([
                'status' => 200,
                'unread' => $user->$countUnread,
            ]);
    }

    protected function markAsRead(Builder $messages, User $user): void
    {
        SupportMessage::whereIn('id', (clone $messages)->select('id')->get())
            ->where('type', 'supportMessage')
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        $notifications = (clone $messages)->with('readMark')->where('type', 'notification')->get();
        $notifications->each(function ($notification) use ($user) {
            if (! $notification->readMark) {
                SupportMessage::create([
                    'message' => $notification->id,
                    'type' => 'notificationRead',
                    'user_id' => $user->id,
                    'read_at' => Carbon::now(),
                ]);
            }
        });
    }
}

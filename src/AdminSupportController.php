<?php

namespace Nikservik\SimpleSupport;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\Requests\StoreSupportMessage;

class AdminSupportController extends Controller
{
    static function routes() {
        Route::domain('admin.'.Str::after(config('app.url'),'//'))
            ->namespace('Nikservik\SimpleSupport')->prefix('support')->group(function () {
            Route::get('search', 'AdminSupportController@search');
            Route::get('{list?}', 'AdminSupportController@index');
            Route::get('dialog/{user}', 'AdminSupportController@show');
            Route::post('dialog/{user}', 'AdminSupportController@store');
            Route::get('message/{message}/delete', 'AdminSupportController@delete');
        });
    }

    public function __construct()
    {
        $this->middleware(['web']);
    }

    public function index($list='')
    {
        $list = $list ? $list : 'all';

        $dialogs = User::whereHas('support_messages', function ($query) use ($list) {
                if ($list == 'unread')
                    $query->where('type', 'userMessage')->whereNull('read_at');
            })
            ->with(['support_messages' => function ($query) use ($list) {
                $query->where('type', 'userMessage')->latest();
                if ($list == 'unread')
                    $query->whereNull('read_at');
            }])
            ->withCount(['support_messages as unread' => function ($query) {
                $query->where('type', 'userMessage')->whereNull('read_at');
            }]) 
            ->paginate(20);

    	return view('simplesupport::admin.index', [
            'dialogs' => $dialogs, 
            'list' => $list, 
            'stats' => $this->stats([$list => $dialogs->total()]),
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $dialogs = User::select(['users.*', 'support_messages.message as message'])
            ->join('support_messages', 'users.id', '=', 'support_messages.user_id')
            ->orWhere('support_messages.message', 'LIKE', '%'.$query.'%')
            ->orderBy('support_messages.created_at', 'DESC')->paginate(10)
            ->appends(['q' => $query]);

        return view('simplesupport::admin.index', [
            'dialogs' => $dialogs, 
            'list' => 'search', 
            'query' => $query,
            'stats' => $this->stats(['search' => $dialogs->total()]),
        ]);
    }

    public function show(User $user)
    {
        $user->support_messages()->where('type', 'userMessage')->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);
        $messages = $user->support_messages()->latest()->paginate(10);

    	return view('simplesupport::admin.show', [
            'messages' => $messages, 'user' => $user, 
        ]);
    }

    public function store(StoreSupportMessage $request, User $user)
    {
        $message = new SupportMessage([
            'message' => $request->get('message'), 
            'user_id' => Auth::id(), 
            'type' => 'supportMessage'
        ]);
        $user->support_messages()->save($message);

        return redirect('/support/dialog/'.$user->id.'#read');
    }

    public function delete(SupportMessage $message)
    {
        $message->delete();

        return redirect()->back();
    }

    protected function stats($stats)
    {
        if (! array_key_exists('all', $stats)) 
            $stats['all'] = User::has('support_messages')->count();

        if (! array_key_exists('unread', $stats)) 
            $stats['unread'] = User::whereHas('support_messages', function ($query) {
                $query->whereNull('read_at');
            })->count();

        return $stats;
    }
}

<?php

namespace Nikservik\SimpleSupport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Nikservik\SimpleSupport\Models\SupportMessage;
use Nikservik\SimpleSupport\NotifyTelegramJob;
use Nikservik\SimpleSupport\Requests\StoreSupportMessage;

class SupportController extends Controller
{
    static function apiRoutes() {
        Route::prefix('api/support')->namespace('Nikservik\SimpleSupport')->group(function () {
            Route::get('', 'SupportController@index');
            Route::get('unread', 'SupportController@unread');
            Route::post('', 'SupportController@store');
        });
    }

    public function __construct()
    {
        $this->middleware(['api', 'auth:api']);
    }

    public function index()
    {
    	$messages = Auth::user()->support_messages()->latest()->paginate(20);
        Auth::user()->support_messages()->where('type', 'supportMessage')->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

    	return ['status' => 'success', 'messages' => $messages];
    }

    public function unread()
    {
        $unread = Auth::user()->support_messages()
            ->where('type', 'supportMessage')->whereNull('read_at')->count();

        return ['status' => 'success', 'unread' => $unread];
    }

    public function store(StoreSupportMessage $request)
    {
        $message = new SupportMessage([
            'message' => $request->message, 
            'user_id' => Auth::user()->id, 
            'type' => 'userMessage'
        ]);
        Auth::user()->support_messages()->save($message);

        NotifyTelegramJob::dispatch($message);

        return $this->index();
    }

}

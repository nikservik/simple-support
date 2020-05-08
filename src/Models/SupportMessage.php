<?php

namespace Nikservik\SimpleSupport\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = [
        'message', 'type', 'user_id',
    ];
    protected $dates = [
        'created_at', 'updated_at', 'read_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function setMessageAttribute($value='')
    {
        $value = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*#?.*)@', '<a href="$1" target="_blank">$1</a>', $value);
        // add "http://" if not set
        $value = preg_replace('/<a\s[^>]*href\s*=\s*"((?!https?:\/\/)[^"]*)"[^>]*>/i', '<a href="http://$1" target="_blank">', $value);

        $this->attributes['message'] = $value;
    }

    public function markRead()
    {
        $this->read_at = Carbon::now();
        $this->save();
    }
}

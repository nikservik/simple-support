<?php

namespace Nikservik\SimpleSupport\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User;

/**
 * @property string $message
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $read_at
 * @property int $user_id
 * @property-read User $user
 */
class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message', 'type', 'user_id', 'read_at', 'reply_to',
    ];
    protected $dates = [
        'created_at', 'updated_at', 'read_at',
    ];

    public function user(): Relation
    {
        return $this->belongsTo(User::class);
    }

    public function readMark(): Relation
    {
        return $this->hasOne(SupportMessage::class, 'message', 'id');
    }

    public function replyTo(): Relation
    {
        return $this->hasOne(SupportMessage::class, 'id', 'reply_to');
    }

    public function setMessageAttribute($value = '')
    {
        $value = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*#?.*)@', '<a href="$1" target="_blank">$1</a>', $value);
        // add "http://" if not set
        $value = preg_replace('/<a\s[^>]*href\s*=\s*"((?!https?:\/\/)[^"]*)"[^>]*>/i', '<a href="http://$1" target="_blank">', $value);

        $this->attributes['message'] = $value;
    }

    public function markRead(): self
    {
        $this->read_at = Carbon::now();
        $this->save();

        return $this;
    }
}

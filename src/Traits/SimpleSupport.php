<?php

namespace Nikservik\SimpleSupport\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Nikservik\SimpleSupport\Models\SupportMessage;

trait SimpleSupport
{
    protected int $countUnread;

    public function supportMessages(): Relation
    {
        return $this->hasMany(SupportMessage::class)
            ->where('type', '<>', 'notificationRead');
        }

    public function getCountUnreadAttribute(): int
    {
        if (isset($this->countUnread)) {
            return $this->countUnread;
        }

        $countUnreadMethod = Config::get('simple-support.unread-count') == 'fast'
            ? 'countUnreadFast'
            : 'countUnreadSimple';

        $this->countUnread = $this->$countUnreadMethod();

        return $this->countUnread;
    }

    protected function countUnreadSimple(): int
    {
        $unreadMessages = $this->supportMessages()->whereNull('support_messages.read_at')->where('support_messages.type', 'supportMessage')->count();
        $readNotifications = SupportMessage::where('user_id', $this->id)->whereNotNull('read_at')->where('type', 'notificationRead')->count();
        $notifications = SupportMessage::where('type', 'notification')->where('created_at', '>=', $this->created_at)->count();

        return $unreadMessages + $notifications - $readNotifications;
    }

    protected function countUnreadFast(): int
    {
        $result = DB::table(DB::raw(
            "(SELECT COUNT(id) as `count` FROM support_messages WHERE user_id = {$this->id} AND read_at IS NULL AND type = 'supportMessage') AS `unread_messages`,"
            . "    (SELECT COUNT(id) as `count` FROM support_messages WHERE user_id = {$this->id} AND type = 'notificationRead') AS `read_notifications`,"
            . "    (SELECT COUNT(id) as `count` FROM support_messages WHERE user_id IS NULL AND type = 'notification' AND created_at >= '{$this->created_at}') AS `notifications`"
        ))->selectRaw(
            "(`unread_messages`.`count` + `notifications`.`count` - `read_notifications`.`count`) as `count`"
        )->get();

        return $result[0]->count;
    }
}

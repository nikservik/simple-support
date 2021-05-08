<?php

namespace Nikservik\SimpleSupport\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Nikservik\SimpleSupport\Models\SupportMessage;

trait SimpleSupport
{
    public function supportMessages(): Relation
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function getCountUnreadAttribute(): int
    {
        $unreadMessages = $this->supportMessages()->whereNull('read_at')->where('support_messages.type', 'supportMessage')->count();
        $readNotifications = $this->supportMessages()->whereNotNull('read_at')->where('support_messages.type', 'notificationRead')->count();
        $notifications = SupportMessage::where('type', 'notification')->count();

        return $unreadMessages + $notifications - $readNotifications;
    }

    public function getCountUnreadFastAttribute(): int
    {
        $result = DB::table(DB::raw(
            "(SELECT COUNT(id) as `count` FROM support_messages WHERE user_id = {$this->id} AND read_at IS NULL AND type = 'supportMessage') AS `unread_messages`,"
            . "    (SELECT COUNT(id) as `count` FROM support_messages WHERE user_id = {$this->id} AND type = 'notificationRead') AS `read_notifications`,"
            . "    (SELECT COUNT(id) as `count` FROM support_messages WHERE user_id IS NULL AND type = 'notification') AS `notifications`"
        ))->selectRaw(
            "(`unread_messages`.`count` + `notifications`.`count` - `read_notifications`.`count`) as `count`"
        )->get();

        return $result[0]->count;
    }
}

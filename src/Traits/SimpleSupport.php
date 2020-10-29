<?php 

namespace Nikservik\SimpleSupport\Traits;

use Nikservik\SimpleSupport\Models\SupportDialog;
use Nikservik\SimpleSupport\Models\SupportMessage;

trait SimpleSupport 
{

    public function support_messages()
    {
        return $this->hasMany(SupportMessage::class);
    }
    public function unreadSupportMessagesCount($type = 'support')
    {
        return $this->support_messages()->whereNull('read_at')->where('support_messages.type', $type.'Message')->count();
    }
}
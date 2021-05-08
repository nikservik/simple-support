<?php


use Nikservik\SimpleSupport\Actions\DeleteSupportMessage;
use Nikservik\SimpleSupport\Actions\GetSupportMessages;
use Nikservik\SimpleSupport\Actions\GetUnreadSupportMessages;
use Nikservik\SimpleSupport\Actions\PostMessageFromUser;
use Nikservik\SimpleSupport\Actions\UpdateSupportMessage;

GetSupportMessages::route();
GetUnreadSupportMessages::route();
PostMessageFromUser::route();
DeleteSupportMessage::route();
UpdateSupportMessage::route();

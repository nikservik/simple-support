<?php


namespace Nikservik\SimpleSupport\Tests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Auth;
use Nikservik\SimpleSupport\Traits\SimpleSupport;

/**
 * Класс пользователя только для тестирования пакета
 * @property string $email
 * @property int $countUnread
 */
class User extends Auth
{
    use SimpleSupport;
    use HasFactory;

    protected $table = 'users';

    protected $fillable = ['email', ];
}

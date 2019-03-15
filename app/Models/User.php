<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable; //Notifiable 是消息通知相关功能引用
use Illuminate\Foundation\Auth\User as Authenticatable; //Authenticatable 是授权相关功能的引用

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function gravatar($size = '100')
    {
        //加密
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }
}
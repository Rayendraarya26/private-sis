<?php

namespace App\Models\BbkkpSis;;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SysUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'sys_user';
    protected $primaryKey = 'user_id';
    protected $guarded = ['user_id'];
    const CREATED_AT = 'user_created_at';
    const UPDATED_AT = 'user_updated_at';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'user_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_last_login' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function getImage()
    {
        return asset($this->user_picture);
    }

    public function user_group()
    {
        return $this->hasMany(SysUserGroup::class, "ug_user_id", "user_id");
    }
}

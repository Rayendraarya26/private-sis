<?php

namespace App\Models\BbkkpSis;;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class SysUser
 *
 * @property int $user_id
 * @property string $user_email
 * @property string|null $user_fullname
 * @property string|null $user_password
 * @property string|null $user_token
 * @property string $user_is_active
 * @property string $user_is_banned
 * @property string|null $user_picture
 * @property Carbon|null $user_last_login
 * @property Carbon|null $user_active_at
 * @property Carbon|null $user_banned_at
 * @property Carbon|null $user_created_at
 * @property Carbon|null $user_updated_at
 *
 * @property SisPelanggan $sis_pelanggan
 * @property Collection|SysUserFbtoken[] $sys_user_fbtokens
 * @property SysUserGroup $sys_user_group
 * @property Collection|SysUserNotif[] $sys_user_notifs
 *
 * @package App\Models\BbkkpSis
 */
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

    protected $fillable = [
        'user_email',
        'user_fullname',
        'user_password',
        'user_token',
        'user_is_active',
        'user_is_banned',
        'user_picture',
        'user_last_login',
        'user_active_at',
        'user_banned_at',
        'user_created_at',
        'user_updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_last_login' => 'datetime',
        'user_active_at' => 'datetime',
        'user_banned_at' => 'datetime',
        'user_created_at' => 'datetime',
        'user_updated_at' => 'datetime',
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

    public function master_pegawais()
    {
        return $this->hasMany(MasterPegawai::class, 'user_id');
    }

    public function sis_audit_lks()
    {
        return $this->hasMany(SisAuditLks::class, 'user_id');
    }

    public function sis_pelanggan()
    {
        return $this->hasOne(SisPelanggan::class, 'user_id');
    }

    public function sis_permohonans()
    {
        return $this->hasMany(SisPermohonan::class, 'user_id');
    }

    public function sys_user_fbtokens()
    {
        return $this->hasMany(SysUserFbtoken::class, 'fbtoken_user_id');
    }

    public function sys_user_group()
    {
        return $this->hasOne(SysUserGroup::class, 'ug_user_id');
    }

    public function sys_user_notifs()
    {
        return $this->hasMany(SysUserNotif::class, 'notif_user_id');
    }
}

<?php

namespace App\Models\BbkkpSis;;

use Illuminate\Database\Eloquent\Model;

class SysUserNotif extends Model
{
    protected $table = 'sys_user_notif';
    protected $primaryKey = 'notif_id';
    protected $guarded = ['notif_id'];
    const CREATED_AT = 'notif_created_at';
    const UPDATED_AT = 'notif_updated_at';

    protected $casts = [
        'notif_created_at' => 'datetime',
        'notif_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(SysUser::class, "notif_user_id", "user_id");
    }
}

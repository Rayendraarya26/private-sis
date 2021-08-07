<?php

namespace App\Models\BbkkpSis;;

use Illuminate\Database\Eloquent\Model;

class SysNotif extends Model
{
    protected $table = 'sys_notif';
    protected $primaryKey = 'notif_id';
    protected $guarded = ['notif_id'];
    const CREATED_AT = 'notif_created_at';
    const UPDATED_AT = 'notif_updated_at';

    public function m_user()
    {
        return $this->belongsTo(SysUser::class, "notif_user_id", "user_id");
    }
}

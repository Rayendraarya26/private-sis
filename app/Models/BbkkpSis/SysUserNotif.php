<?php

namespace App\Models\BbkkpSis;;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysUserNotif
 *
 * @property int $notif_id
 * @property int|null $notif_user_id
 * @property string|null $notif_title
 * @property string|null $notif_content
 * @property string|null $notif_link
 * @property string|null $notif_is_read
 * @property Carbon|null $notif_created_at
 * @property Carbon|null $notif_updated_at
 *
 * @property SysUser|null $sys_user
 *
 * @package App\Models\BbkkpSis
 */
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

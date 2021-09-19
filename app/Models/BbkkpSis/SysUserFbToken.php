<?php

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysUserFbtoken
 *
 * @property int $fbtoken_id
 * @property int|null $fbtoken_user_id
 * @property string|null $fbtoken_token
 * @property string|null $fbtoken_agent
 * @property string|null $fbtoken_ip
 * @property Carbon|null $fbtoken_created_at
 * @property Carbon|null $fbtoken_updated_at
 *
 * @property SysUser|null $user
 *
 * @package App\Models\BbkkpSis
 */
class SysUserFbToken extends Model
{
    protected $table = 'sys_user_fbtoken';
    protected $primaryKey = 'fbtoken_id';
    protected $guarded = ['fbtoken_id'];
    const CREATED_AT = 'fbtoken_created_at';
    const UPDATED_AT = 'fbtoken_updated_at';

    protected $casts = [
        'fbtoken_user_id' => 'int',
        'fbtoken_created_at' => 'datetime',
        'fbtoken_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(SysUser::class, 'fbtoken_user_id', 'user_id');
    }
}

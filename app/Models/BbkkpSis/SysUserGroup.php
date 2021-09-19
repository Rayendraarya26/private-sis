<?php

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysUserGroup
 *
 * @property int $ug_user_id
 * @property int $ug_group_id
 * @property string|null $ug_is_default
 * @property Carbon|null $ug_created_at
 * @property Carbon|null $ug_updated_at
 *
 * @property SysGroup $sys_group
 * @property SysUser $sys_user
 *
 * @package App\Models\BbkkpSis
 */
class SysUserGroup extends Model
{
    protected $table = 'sys_user_group';
    const CREATED_AT = 'ug_created_at';
    const UPDATED_AT = 'ug_updated_at';

    protected $casts = [
        'ug_user_id' => 'int',
        'ug_group_id' => 'int',
        'ug_created_at' => 'datetime',
        'ug_updated_at' => 'datetime',
    ];

    protected $fillable = [
        'ug_user_id',
        'ug_group_id',
        'ug_is_default',
        'ug_created_at',
        'ug_updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(SysUser::class, "ug_user_id", "user_id");
    }

    public function group()
    {
        return $this->belongsTo(SysGroup::class, "ug_group_id", "group_id");
    }
}

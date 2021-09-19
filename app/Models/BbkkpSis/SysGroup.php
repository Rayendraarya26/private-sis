<?php

namespace App\Models\BbkkpSis;;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysGroup
 *
 * @property int $group_id
 * @property string $group_name
 * @property string|null $group_desc
 * @property string $group_is_active
 * @property Carbon|null $group_created_at
 * @property Carbon|null $group_updated_at
 *
 * @property Collection|SysGroupPermission[] $sys_group_permissions
 * @property SysUserGroup $sys_user_group
 *
 * @package App\Models\BbkkpSis
 */
class SysGroup extends Model
{
    protected $table = 'sys_group';
    protected $primaryKey = 'group_id';
    protected $guarded = ['group_id'];
    const CREATED_AT = 'group_created_at';
    const UPDATED_AT = 'group_updated_at';

    protected $casts = [
        'group_created_at' => 'datetime',
        'group_updated_at' => 'datetime',
    ];

    protected $fillable = [
        'group_name',
        'group_desc',
        'group_is_active',
        'group_created_at',
        'group_updated_at'
    ];

    function permission()
    {
        return $this->hasMany(SysGroupPermission::class, "group_id", "group_id");
    }
}

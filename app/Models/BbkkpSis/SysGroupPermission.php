<?php

namespace App\Models\BbkkpSis;;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SysGroupPermission
 *
 * @property int $permission_id
 * @property int $group_id
 * @property int $action_id
 *
 *
 * @package App\Models\BbkkpSis
 */
class SysGroupPermission extends Model
{
    protected $table = 'sys_group_permission';
    protected $primaryKey = 'permission_id';
    protected $guarded = ['permission_id'];
    public $timestamps = false;

    public function menu_action()
    {
        return $this->belongsTo(SysMenuAction::class, "action_id", "action_id");
    }
}

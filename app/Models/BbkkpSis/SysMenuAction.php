<?php

namespace App\Models\BbkkpSis;;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysMenuAction
 *
 * @property int $action_id
 * @property int $action_menu_id
 * @property string $action_name
 * @property string $action_controller
 * @property Carbon|null $action_created_at
 * @property Carbon|null $action_updated_at
 *
 * @property SysMenu $menu
 *
 * @package App\Models\BbkkpSis
 */
class SysMenuAction extends Model
{
    protected $table = 'sys_menu_action';
    protected $primaryKey = 'action_id';
    protected $guarded = ['action_id'];
    const CREATED_AT = 'action_created_at';
    const UPDATED_AT = 'action_updated_at';

    protected $casts = [
        'action_created_at' => 'datetime',
        'action_updated_at' => 'datetime',
    ];

    public function menu()
    {
        return $this->belongsTo(SysMenu::class, "action_menu_id", "menu_id");
    }

    public function group_permissions()
    {
        return $this->hasMany(SysGroupPermission::class, 'action_id', 'action_id');
    }
}

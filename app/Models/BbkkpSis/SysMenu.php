<?php

namespace App\Models\BbkkpSis;
;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysMenu
 *
 * @property int $menu_id
 * @property int|null $menu_parent_id
 * @property string|null $menu_name
 * @property string|null $menu_desc
 * @property string $menu_is_active
 * @property string|null $menu_icon
 * @property int|null $menu_order
 * @property Carbon|null $menu_created_at
 * @property Carbon|null $menu_updated_at
 *
 * @property Collection|SysMenuAction[] $action
 *
 * @package App\Models\BbkkpSis
 */
class SysMenu extends Model
{
    protected $table = 'sys_menu';
    protected $primaryKey = 'menu_id';
    protected $guarded = ['menu_id'];
    const CREATED_AT = 'menu_created_at';
    const UPDATED_AT = 'menu_updated_at';

    protected $casts = [
        'menu_created_at' => 'datetime',
        'menu_updated_at' => 'datetime',
    ];

    function action()
    {
        return $this->hasMany(SysMenuAction::class, "action_menu_id", "menu_id");
    }
}

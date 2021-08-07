<?php

namespace App\Models\BbkkpSis;;

use Illuminate\Database\Eloquent\Model;

class SysMenuAction extends Model
{
    protected $table = 'sys_menu_action';
    protected $primaryKey = 'action_id';
    protected $guarded = ['action_id'];
    const CREATED_AT = 'action_created_at';
    const UPDATED_AT = 'action_updated_at';

    public function menu()
    {
        return $this->belongsTo(SysMenu::class, "action_menu_id", "menu_id");
    }
}

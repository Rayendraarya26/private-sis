<?php

namespace App\Models\BbkkpSis;;

use Illuminate\Database\Eloquent\Model;

class SysMenu extends Model
{
    protected $table = 'sys_menu';
    protected $primaryKey = 'menu_id';
    protected $guarded = ['menu_id'];
    const CREATED_AT = 'menu_created_at';
    const UPDATED_AT = 'menu_updated_at';

    function action(){
        return $this->hasMany(SysMenuAction::class, "action_menu_id", "menu_id");
    }
}

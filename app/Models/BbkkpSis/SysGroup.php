<?php

namespace App\Models\BbkkpSis;;

use Illuminate\Database\Eloquent\Model;

class SysGroup extends Model
{
    protected $table = 'sys_group';
    protected $primaryKey = 'group_id';
    protected $guarded = ['group_id'];
    const CREATED_AT = 'group_created_at';
    const UPDATED_AT = 'group_updated_at';

    function permission()
    {
        return $this->hasMany(SysGroupPermission::class, "group_id", "group_id");
    }
}

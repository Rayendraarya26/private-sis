<?php

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

class SysUserGroup extends Model
{
    protected $table = 'sys_user_group';
    protected $fillable = ['ug_user_id', 'ug_group_id', 'ug_is_default', 'ug_created_at', 'ug_updated_at'];
    const CREATED_AT = 'ug_created_at';
    const UPDATED_AT = 'ug_updated_at';

    protected $casts = [
        'ug_created_at' => 'datetime',
        'ug_updated_at' => 'datetime',
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

<?php

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

class SysUserFbToken extends Model
{
    protected $table = 'sys_user_fbtoken';
    protected $primaryKey = 'fbtoken_id';
    protected $guarded = ['fbtoken_id'];
    const CREATED_AT = 'fbtoken_created_at';
    const UPDATED_AT = 'fbtoken_updated_at';

    protected $casts = [
        'fbtoken_created_at' => 'datetime',
        'fbtoken_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(SysUser::class, 'fbtoken_user_id', 'user_id');
    }
}

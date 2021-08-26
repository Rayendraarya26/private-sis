<?php

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

class MasterIcons extends Model
{
    protected $table = 'master_icons';
    protected $primaryKey = 'icon_id';
    protected $guarded = ['icon_id'];
    const CREATED_AT = 'icon_created_at';
    const UPDATED_AT = 'icon_updated_at';

    protected $casts = [
        'icon_created_at' => 'datetime',
        'icon_updated_at' => 'datetime',
    ];

    protected $fillable = [
        'icon_name',
        'icon_created_at',
        'icon_updated_at'
    ];
}

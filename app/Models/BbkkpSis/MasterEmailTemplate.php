<?php

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

class MasterEmailTemplate extends Model
{
    protected $table = 'master_email_template';
    protected $primaryKey = 'template_id';
    protected $guarded = ['template_id'];
    const CREATED_AT = 'template_created_at';
    const UPDATED_AT = 'template_updated_at';

    protected $casts = [
        'template_created_at' => 'datetime',
        'template_updated_at' => 'datetime',
    ];
}

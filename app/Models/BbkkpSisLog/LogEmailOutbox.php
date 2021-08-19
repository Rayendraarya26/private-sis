<?php

namespace App\Models\BbkkpSisLog;

use Illuminate\Database\Eloquent\Model;

class LogEmailOutbox extends Model
{
    protected $connection = 'mysql_log';

    protected $table = 'log_email_outbox';
    protected $primaryKey = 'outbox_id';
    protected $guarded = ['outbox_id'];
    const CREATED_AT = 'outbox_created_at';
    const UPDATED_AT = 'outbox_updated_at';

    protected $casts = [
        'outbox_read_at' => 'datetime',
        'outbox_created_at' => 'datetime',
        'outbox_updated_at' => 'datetime',
    ];
}

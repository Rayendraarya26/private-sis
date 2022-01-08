<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLksLog
 * 
 * @property int $lkslog_id
 * @property int|null $lks_revisi_id
 * @property string $lkslog_data
 * @property string|null $lkslog_file
 * @property Carbon $lkslog_created_at
 * @property int $lkslog_created_id
 * 
 * @property SysUser $sys_user
 * @property SisAuditLksRevisi|null $sis_audit_lks_revisi
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLksLog extends Model
{
	protected $table = 'sis_audit_lks_log';
	protected $primaryKey = 'lkslog_id';
	public $timestamps = false;

	protected $casts = [
		'lks_revisi_id' => 'int',
		'lkslog_created_id' => 'int'
	];

	protected $dates = [
		'lkslog_created_at'
	];

	protected $fillable = [
		'lks_revisi_id',
		'lkslog_data',
		'lkslog_file',
		'lkslog_created_at',
		'lkslog_created_id'
	];

	public function sys_user()
	{
		return $this->belongsTo(SysUser::class, 'lkslog_created_id');
	}

	public function sis_audit_lks_revisi()
	{
		return $this->belongsTo(SisAuditLksRevisi::class, 'lks_revisi_id');
	}
}

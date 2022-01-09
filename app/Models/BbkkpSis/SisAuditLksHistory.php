<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLksHistory
 * 
 * @property int $lkshistory_id
 * @property int|null $lks_revisi_id
 * @property string $lkshistory_data
 * @property string|null $lkshistory_file
 * @property Carbon $lkshistory_created_at
 * @property int $lkshistory_created_id
 * 
 * @property SysUser $sys_user
 * @property SisAuditLksRevisi|null $sis_audit_lks_revisi
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLksHistory extends Model
{
	protected $table = 'sis_audit_lks_history';
	protected $primaryKey = 'lkshistory_id';
	public $timestamps = false;

	protected $casts = [
		'lks_revisi_id' => 'int',
		'lkshistory_created_id' => 'int'
	];

	protected $dates = [
		'lkshistory_created_at'
	];

	protected $fillable = [
		'lks_revisi_id',
		'lkshistory_data',
		'lkshistory_file',
		'lkshistory_created_at',
		'lkshistory_created_id'
	];

	public function sys_user()
	{
		return $this->belongsTo(SysUser::class, 'lkshistory_created_id');
	}

	public function sis_audit_lks_revisi()
	{
		return $this->belongsTo(SisAuditLksRevisi::class, 'lks_revisi_id');
	}
}

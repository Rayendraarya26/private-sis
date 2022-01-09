<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLksRevisi
 * 
 * @property int $lks_revisi_id
 * @property int $lks_id
 * @property string|null $lks_revisi_catatan
 * @property string|null $lks_revisi_oleh
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditLks $sis_audit_lks
 * @property SisAuditLksHistory $sis_audit_lks_history
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLksRevisi extends Model
{
	protected $table = 'sis_audit_lks_revisi';
	protected $primaryKey = 'lks_revisi_id';

	protected $casts = [
		'lks_id' => 'int'
	];

	protected $fillable = [
		'lks_id',
		'lks_revisi_catatan',
		'lks_revisi_oleh'
	];

	public function sis_audit_lks()
	{
		return $this->belongsTo(SisAuditLks::class, 'lks_id');
	}

	public function sis_audit_lks_history()
	{
		return $this->hasOne(SisAuditLksHistory::class, 'lks_revisi_id');
	}
}

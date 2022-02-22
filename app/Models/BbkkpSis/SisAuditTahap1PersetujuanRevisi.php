<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1PersetujuanRevisi
 * 
 * @property int $aud_thp1_perseujuan_revisi_id
 * @property int|null $aud_thp1_id
 * @property string|null $aud_thp1_perseujuan_revisi_catatan
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property SisAuditTahap1|null $sis_audit_tahap1
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1PersetujuanRevisi extends Model
{
	protected $table = 'sis_audit_tahap1_persetujuan_revisi';
	protected $primaryKey = 'aud_thp1_perseujuan_revisi_id';

	protected $casts = [
		'aud_thp1_id' => 'int'
	];

	protected $fillable = [
		'aud_thp1_id',
		'aud_thp1_perseujuan_revisi_catatan'
	];

	public function sis_audit_tahap1()
	{
		return $this->belongsTo(SisAuditTahap1::class, 'aud_thp1_id');
	}
}

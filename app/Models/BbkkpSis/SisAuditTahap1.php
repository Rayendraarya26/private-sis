<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1
 * 
 * @property int $aud_thp1_id
 * @property int $jadw_audit_id
 * @property string|null $aud_thp1_status_audit
 * @property string|null $aud_thp1_kesimpulan
 * @property string|null $aud_thp1_rekomendasi
 * @property string|null $aud_thp1_ditijau_oleh
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwalAudit $sis_jadwal_audit
 * @property Collection|SisAuditDetailTahap1[] $sis_audit_detail_tahap1s
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1 extends Model
{
	protected $table = 'sis_audit_tahap1';
	protected $primaryKey = 'aud_thp1_id';

	protected $casts = [
		'jadw_audit_id' => 'int'
	];

	protected $fillable = [
		'jadw_audit_id',
		'aud_thp1_status_audit',
		'aud_thp1_kesimpulan',
		'aud_thp1_rekomendasi',
		'aud_thp1_ditijau_oleh'
	];

	public function sis_jadwal_audit()
	{
		return $this->belongsTo(SisJadwalAudit::class, 'jadw_audit_id');
	}

	public function sis_audit_detail_tahap1s()
	{
		return $this->hasMany(SisAuditDetailTahap1::class, 'aud_thp1_id');
	}
}

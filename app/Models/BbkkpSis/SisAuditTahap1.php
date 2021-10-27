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
 * @property string|null $aud_thp1_kolom_v
 * @property string|null $aud_thp1_kolom_vi
 * @property string|null $aud_thp1_kolom_vii
 * @property string|null $aud_thp1_kolom_viii
 * @property string|null $aud_thp1_kolom_ix
 * @property string|null $aud_thp1_kolom_x
 * @property string|null $aud_thp1_kolom_xi
 * @property string|null $aud_thp1_kolom_xii
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
		'aud_thp1_kolom_v',
		'aud_thp1_kolom_vi',
		'aud_thp1_kolom_vii',
		'aud_thp1_kolom_viii',
		'aud_thp1_kolom_ix',
		'aud_thp1_kolom_x',
		'aud_thp1_kolom_xi',
		'aud_thp1_kolom_xii'
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

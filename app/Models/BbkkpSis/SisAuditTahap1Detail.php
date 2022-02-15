<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1Detail
 * 
 * @property int $aud_thp1_det_id
 * @property int $aud_thp1_id
 * @property int|null $klausul_thp1_id
 * @property string|null $aud_thp1_det_is_tinjauan
 * @property string|null $aud_thp1_det_thp1_nomor
 * @property string|null $aud_thp1_det_peryataan
 * @property string|null $aud_thp1_det_kode_dok
 * @property string|null $aud_thp1_det_judul_dok
 * @property string|null $aud_thp1_det_keterangan
 * @property string|null $aud_thp1_det_persyaratan
 * @property string|null $aud_thp1_det_nilai
 * @property string|null $aud_thp1_det_satuan
 * @property string|null $aud_thp1_det_hasil_tinjauan
 * @property string|null $aud_thp1_det_status
 * @property Carbon|null $aud_thp1_det_tanggal_ditutup
 * @property string|null $aud_thp1_det_keterangan_ditutup
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditTahap1 $sis_audit_tahap1
 * @property Collection|SisAuditTahap1Revisi[] $sis_audit_tahap1_revisis
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1Detail extends Model
{
	protected $table = 'sis_audit_tahap1_detail';
	protected $primaryKey = 'aud_thp1_det_id';

	protected $casts = [
		'aud_thp1_id' => 'int',
		'klausul_thp1_id' => 'int'
	];

	protected $dates = [
		'aud_thp1_det_tanggal_ditutup'
	];

	protected $fillable = [
		'aud_thp1_id',
		'klausul_thp1_id',
		'aud_thp1_det_is_tinjauan',
		'aud_thp1_det_thp1_nomor',
		'aud_thp1_det_peryataan',
		'aud_thp1_det_kode_dok',
		'aud_thp1_det_judul_dok',
		'aud_thp1_det_keterangan',
		'aud_thp1_det_persyaratan',
		'aud_thp1_det_nilai',
		'aud_thp1_det_satuan',
		'aud_thp1_det_hasil_tinjauan',
		'aud_thp1_det_status',
		'aud_thp1_det_tanggal_ditutup',
		'aud_thp1_det_keterangan_ditutup'
	];

	public function sis_audit_tahap1()
	{
		return $this->belongsTo(SisAuditTahap1::class, 'aud_thp1_id');
	}

	public function sis_audit_tahap1_revisis()
	{
		return $this->hasMany(SisAuditTahap1Revisi::class, 'aud_thp1_det_id');
	}
}

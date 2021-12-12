<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanDetail
 * 
 * @property int $mohon_det_id
 * @property int|null $mohon_id
 * @property string|null $mohon_det_jenis_status
 * @property int|null $cust_sert_id
 * @property int|null $sert_id
 * @property string|null $mohon_det_perlu_tahap1
 * @property string|null $mohon_det_no_referensi
 * @property float|null $mohon_det_harga_permohonan
 * 
 * @property SisPermohonan|null $sis_permohonan
 * @property SisPelangganSertifikasi|null $sis_pelanggan_sertifikasi
 * @property MasterSertifikasi|null $master_sertifikasi
 * @property Collection|SisAuditTahap1[] $sis_audit_tahap1s
 * @property Collection|SisBillingItems[] $sis_billing_items
 * @property Collection|SisJadwalAudit[] $sis_jadwal_audits
 * @property Collection|SisPermohonanKomoditi[] $sis_permohonan_komoditis
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanDetail extends Model
{
	protected $table = 'sis_permohonan_detail';
	protected $primaryKey = 'mohon_det_id';
	public $timestamps = false;

	protected $casts = [
		'mohon_id' => 'int',
		'cust_sert_id' => 'int',
		'sert_id' => 'int',
		'mohon_det_harga_permohonan' => 'float'
	];

	protected $fillable = [
		'mohon_id',
		'mohon_det_jenis_status',
		'cust_sert_id',
		'sert_id',
		'mohon_det_perlu_tahap1',
		'mohon_det_no_referensi',
		'mohon_det_harga_permohonan'
	];

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}

	public function sis_pelanggan_sertifikasi()
	{
		return $this->belongsTo(SisPelangganSertifikasi::class, 'cust_sert_id');
	}

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}

	public function sis_audit_tahap1s()
	{
		return $this->hasMany(SisAuditTahap1::class, 'mohon_det_id');
	}

	public function sis_billing_items()
	{
		return $this->hasMany(SisBillingItems::class, 'mohon_det_id');
	}

	public function sis_jadwal_audits()
	{
		return $this->hasMany(SisJadwalAudit::class, 'mohon_det_id');
	}

	public function sis_permohonan_komoditis()
	{
		return $this->hasMany(SisPermohonanKomoditi::class, 'mohon_det_id');
	}
}

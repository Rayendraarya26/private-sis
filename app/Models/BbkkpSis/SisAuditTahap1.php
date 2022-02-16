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
 * @property int|null $bill_id
 * @property int $mohon_id
 * @property int $mohon_det_id
 * @property string|null $sert_tahap1_jenis
 * @property string|null $aud_thp1_status
 * @property string|null $aud_thp1_status_temuan
 * @property string|null $aud_thp1_ditutup
 * @property Carbon|null $aud_thp1_tanggal_mulai
 * @property Carbon|null $aud_thp1_tanggal_selesai
 * @property string|null $aud_thp1_file_jadwal
 * @property string|null $aud_thp1_jenis
 * @property string|null $aud_thp1_tujuan
 * @property string|null $aud_thp1_standart_acuan
 * @property string|null $aud_thp1_file_temuan
 * @property string|null $aud_thp1_file_notulen
 * @property string|null $aud_thp1_file_daftar_hadir
 * @property Carbon|null $aud_thp1_tanggal_rapat_akhir
 * @property string|null $aud_thp1_kolom_v
 * @property string|null $aud_thp1_kolom_vi
 * @property string|null $aud_thp1_kolom_vii
 * @property string|null $aud_thp1_kolom_viii
 * @property string|null $aud_thp1_kolom_ix
 * @property string|null $aud_thp1_kolom_x
 * @property string|null $aud_thp1_kolom_xi
 * @property string|null $aud_thp1_kolom_xii
 * @property string|null $aud_thp1_nomor
 * @property string|null $aud_thp1_kesimpulan
 * @property string|null $aud_thp1_pernyataan_auditor
 * @property string|null $aud_thp1_notulen
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisBilling|null $sis_billing
 * @property SisPermohonan $sis_permohonan
 * @property SisPermohonanDetail $sis_permohonan_detail
 * @property Collection|SisAuditTahap1Detail[] $sis_audit_tahap1_details
 * @property Collection|SisAuditTahap1Tim[] $sis_audit_tahap1_tims
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1 extends Model
{
	protected $table = 'sis_audit_tahap1';
	protected $primaryKey = 'aud_thp1_id';

	protected $casts = [
		'bill_id' => 'int',
		'mohon_id' => 'int',
		'mohon_det_id' => 'int'
	];

	protected $dates = [
		'aud_thp1_tanggal_mulai',
		'aud_thp1_tanggal_selesai',
		'aud_thp1_tanggal_rapat_akhir'
	];

	protected $fillable = [
		'bill_id',
		'mohon_id',
		'mohon_det_id',
		'sert_tahap1_jenis',
		'aud_thp1_status',
		'aud_thp1_status_temuan',
		'aud_thp1_ditutup',
		'aud_thp1_tanggal_mulai',
		'aud_thp1_tanggal_selesai',
		'aud_thp1_file_jadwal',
		'aud_thp1_jenis',
		'aud_thp1_tujuan',
		'aud_thp1_standart_acuan',
		'aud_thp1_file_temuan',
		'aud_thp1_file_notulen',
		'aud_thp1_file_daftar_hadir',
		'aud_thp1_tanggal_rapat_akhir',
		'aud_thp1_kolom_v',
		'aud_thp1_kolom_vi',
		'aud_thp1_kolom_vii',
		'aud_thp1_kolom_viii',
		'aud_thp1_kolom_ix',
		'aud_thp1_kolom_x',
		'aud_thp1_kolom_xi',
		'aud_thp1_kolom_xii',
		'aud_thp1_nomor',
		'aud_thp1_kesimpulan',
		'aud_thp1_pernyataan_auditor',
		'aud_thp1_notulen'
	];

	public function sis_billing()
	{
		return $this->belongsTo(SisBilling::class, 'bill_id');
	}

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}

	public function sis_permohonan_detail()
	{
		return $this->belongsTo(SisPermohonanDetail::class, 'mohon_det_id');
	}

	public function sis_audit_tahap1_details()
	{
		return $this->hasMany(SisAuditTahap1Detail::class, 'aud_thp1_id');
	}

	public function sis_audit_tahap1_tims()
	{
		return $this->hasMany(SisAuditTahap1Tim::class, 'aud_thp1_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLapLengkap
 * 
 * @property int $lap_lengkp_id
 * @property int $jadw_id
 * @property string|null $lap_lengkp_penilaian
 * @property string|null $lap_lengkp_penyimpangan
 * @property string|null $lap_lengkp_isu_berdampak
 * @property string|null $lap_lengkp_isu_tidak_terselesaikan
 * @property string|null $lap_lengkp_perubahan
 * @property string|null $lap_lengkp_kekuatan
 * @property string|null $lap_lengkp_kelemahan
 * @property string|null $lap_lengkp_tinjauan_keluhan
 * @property string|null $lap_lengkp_pengendalian_penggunaan
 * @property string|null $lap_lengkp_kedalaman_audit
 * @property string|null $lap_lengkp_pernyataan_kesesuaian
 * @property string|null $lap_lengkp_kesimpulan_ketaatan
 * @property string|null $lap_lengkp_konfirmasi_tujuan
 * @property string|null $lap_lengkp_saran
 * @property string|null $lap_lengkp_kesimpulan
 * @property string|null $lap_lengkp_rekomendasi_lks
 * @property string|null $lap_lengkp_verifikasi_status
 * @property string|null $lap_lengkp_revisi_note
 * @property string|null $lap_lengkp_verifikasi_diajukan
 * @property Carbon|null $lap_lengkp_verifikasi_tanggal
 * @property string|null $lap_lengkp_verifikasi_oleh
 * @property string|null $lap_lengkp_verifikasi_jabatan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLapLengkap extends Model
{
	protected $table = 'sis_audit_lap_lengkap';
	protected $primaryKey = 'lap_lengkp_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $dates = [
		'lap_lengkp_verifikasi_tanggal'
	];

	protected $fillable = [
		'jadw_id',
		'lap_lengkp_penilaian',
		'lap_lengkp_penyimpangan',
		'lap_lengkp_isu_berdampak',
		'lap_lengkp_isu_tidak_terselesaikan',
		'lap_lengkp_perubahan',
		'lap_lengkp_kekuatan',
		'lap_lengkp_kelemahan',
		'lap_lengkp_tinjauan_keluhan',
		'lap_lengkp_pengendalian_penggunaan',
		'lap_lengkp_kedalaman_audit',
		'lap_lengkp_pernyataan_kesesuaian',
		'lap_lengkp_kesimpulan_ketaatan',
		'lap_lengkp_konfirmasi_tujuan',
		'lap_lengkp_saran',
		'lap_lengkp_kesimpulan',
		'lap_lengkp_rekomendasi_lks',
		'lap_lengkp_verifikasi_status',
		'lap_lengkp_revisi_note',
		'lap_lengkp_verifikasi_diajukan',
		'lap_lengkp_verifikasi_tanggal',
		'lap_lengkp_verifikasi_oleh',
		'lap_lengkp_verifikasi_jabatan'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

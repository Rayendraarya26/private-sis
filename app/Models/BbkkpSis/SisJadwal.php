<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisJadwal
 * 
 * @property int $jadw_id
 * @property int|null $bill_id
 * @property string $jadw_tanggal_status
 * @property Carbon $jadw_tanggal_mulai
 * @property Carbon|null $jadw_tanggal_selesai
 * @property string $jadw_team_status
 * @property string|null $jadw_setujui_nama
 * @property string|null $jadw_setujui_jabatan
 * @property string|null $jadw_setujui_temuan
 * @property Carbon|null $jadw_tanggal_rapat_akhir
 * @property string|null $jadw_notulen_rapat
 * @property string|null $jadw_file_laporan_ringkas
 * @property string|null $jadw_file_lks
 * @property string|null $jadw_lks_rekomendasi
 * @property string|null $jadw_file_surat_tugas
 * @property string|null $jadw_file_kehadiran
 * @property string|null $jadw_file_kehadiran_komite
 * @property string|null $jadw_jenis
 * @property string|null $jadw_is_khusus_komite
 * @property string|null $jadw_berita_acara_nomor
 * @property Carbon|null $jadw_berita_acara_tanggal
 * @property string|null $jadw_is_tutup
 * @property int $cust_id
 * @property string|null $jadw_file_jadwal
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisBilling|null $sis_billing
 * @property SisPelanggan $sis_pelanggan
 * @property SisAuditKomitePeriksa $sis_audit_komite_periksa
 * @property SisAuditKomiteRekomendasi $sis_audit_komite_rekomendasi
 * @property SisAuditLapLengkap $sis_audit_lap_lengkap
 * @property SisAuditLapRingkas $sis_audit_lap_ringkas
 * @property Collection|SisAuditLks[] $sis_audit_lks
 * @property Collection|SisAuditObservasi[] $sis_audit_observasis
 * @property Collection|SisAuditPpc[] $sis_audit_ppcs
 * @property Collection|SisAuditSertifikatProduk[] $sis_audit_sertifikat_produks
 * @property Collection|SisAuditTimKomite[] $sis_audit_tim_komites
 * @property Collection|SisJadwalAudit[] $sis_jadwal_audits
 * @property Collection|SisJadwalLog[] $sis_jadwal_logs
 * @property Collection|SisJadwalTim[] $sis_jadwal_tims
 *
 * @package App\Models\BbkkpSis
 */
class SisJadwal extends Model
{
	protected $table = 'sis_jadwal';
	protected $primaryKey = 'jadw_id';

	protected $casts = [
		'bill_id' => 'int',
		'cust_id' => 'int'
	];

	protected $dates = [
		'jadw_tanggal_mulai',
		'jadw_tanggal_selesai',
		'jadw_tanggal_rapat_akhir',
		'jadw_berita_acara_tanggal'
	];

	protected $fillable = [
		'bill_id',
		'jadw_tanggal_status',
		'jadw_tanggal_mulai',
		'jadw_tanggal_selesai',
		'jadw_team_status',
		'jadw_setujui_nama',
		'jadw_setujui_jabatan',
		'jadw_setujui_temuan',
		'jadw_tanggal_rapat_akhir',
		'jadw_notulen_rapat',
		'jadw_file_laporan_ringkas',
		'jadw_file_lks',
		'jadw_lks_rekomendasi',
		'jadw_file_surat_tugas',
		'jadw_file_kehadiran',
		'jadw_file_kehadiran_komite',
		'jadw_jenis',
		'jadw_is_khusus_komite',
		'jadw_berita_acara_nomor',
		'jadw_berita_acara_tanggal',
		'jadw_is_tutup',
		'cust_id',
		'jadw_file_jadwal'
	];

	public function sis_billing()
	{
		return $this->belongsTo(SisBilling::class, 'bill_id');
	}

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}

	public function sis_audit_komite_periksa()
	{
		return $this->hasOne(SisAuditKomitePeriksa::class, 'jadw_id');
	}

	public function sis_audit_komite_rekomendasi()
	{
		return $this->hasOne(SisAuditKomiteRekomendasi::class, 'jadw_id');
	}

	public function sis_audit_lap_lengkap()
	{
		return $this->hasOne(SisAuditLapLengkap::class, 'jadw_id');
	}

	public function sis_audit_lap_ringkas()
	{
		return $this->hasOne(SisAuditLapRingkas::class, 'jadw_id');
	}

	public function sis_audit_lks()
	{
		return $this->hasMany(SisAuditLks::class, 'jadw_id');
	}

	public function sis_audit_observasis()
	{
		return $this->hasMany(SisAuditObservasi::class, 'jadw_id');
	}

	public function sis_audit_ppcs()
	{
		return $this->hasMany(SisAuditPpc::class, 'jadw_id');
	}

	public function sis_audit_sertifikat_produks()
	{
		return $this->hasMany(SisAuditSertifikatProduk::class, 'jadw_id');
	}

	public function sis_audit_tim_komites()
	{
		return $this->hasMany(SisAuditTimKomite::class, 'jadw_id');
	}

	public function sis_jadwal_audits()
	{
		return $this->hasMany(SisJadwalAudit::class, 'jadw_id');
	}

	public function sis_jadwal_logs()
	{
		return $this->hasMany(SisJadwalLog::class, 'jadw_id');
	}

	public function sis_jadwal_tims()
	{
		return $this->hasMany(SisJadwalTim::class, 'jadw_id');
	}
}

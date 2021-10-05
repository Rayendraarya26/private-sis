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
 * @property string|null $jadw_bil_nomor
 * @property float|null $jadw_bil_total
 * @property string|null $jadw_bil_harus_lunas
 * @property string|null $jadw_bil_status
 * @property string|null $jadw_bil_invoice
 * @property string|null $jadw_bil_bukti_bayar
 * @property string|null $jadw_tanggal_status
 * @property Carbon $jadw_tanggal_mulai
 * @property Carbon|null $jadw_tanggal_selesai
 * @property string|null $jadw_jenis
 * @property string|null $jadw_team_status
 * @property string|null $jadw_team_alasan
 * @property int $cust_id
 * @property string|null $jadw_file_jadwal
 * @property string|null $jadw_status_audit
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisPelanggan $sis_pelanggan
 * @property Collection|SisAuditDaftarPeriksa[] $sis_audit_daftar_periksas
 * @property SisAuditKomitePeriksa $sis_audit_komite_periksa
 * @property Collection|SisAuditLapLengkap[] $sis_audit_lap_lengkaps
 * @property Collection|SisAuditLapRingkas[] $sis_audit_lap_ringkas
 * @property Collection|SisAuditLks[] $sis_audit_lks
 * @property Collection|SisAuditLogbook[] $sis_audit_logbooks
 * @property Collection|SisAuditObservasi[] $sis_audit_observasis
 * @property Collection|SisAuditPpc[] $sis_audit_ppcs
 * @property Collection|SisAuditSertifikatProduk[] $sis_audit_sertifikat_produks
 * @property Collection|SisAuditTimKomite[] $sis_audit_tim_komites
 * @property Collection|SisJadwalAudit[] $sis_jadwal_audits
 * @property Collection|SisJadwalTim[] $sis_jadwal_tims
 *
 * @package App\Models\BbkkpSis
 */
class SisJadwal extends Model
{
	protected $table = 'sis_jadwal';
	protected $primaryKey = 'jadw_id';

	protected $casts = [
		'jadw_bil_total' => 'float',
		'cust_id' => 'int'
	];

	protected $dates = [
		'jadw_tanggal_mulai',
		'jadw_tanggal_selesai'
	];

	protected $fillable = [
		'jadw_bil_nomor',
		'jadw_bil_total',
		'jadw_bil_harus_lunas',
		'jadw_bil_status',
		'jadw_bil_invoice',
		'jadw_bil_bukti_bayar',
		'jadw_tanggal_status',
		'jadw_tanggal_mulai',
		'jadw_tanggal_selesai',
		'jadw_jenis',
		'jadw_team_status',
		'jadw_team_alasan',
		'cust_id',
		'jadw_file_jadwal',
		'jadw_status_audit'
	];

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}

	public function sis_audit_daftar_periksas()
	{
		return $this->hasMany(SisAuditDaftarPeriksa::class, 'jadw_id');
	}

	public function sis_audit_komite_periksa()
	{
		return $this->hasOne(SisAuditKomitePeriksa::class, 'jadw_id');
	}

	public function sis_audit_lap_lengkaps()
	{
		return $this->hasMany(SisAuditLapLengkap::class, 'jadw_id');
	}

	public function sis_audit_lap_ringkas()
	{
		return $this->hasMany(SisAuditLapRingkas::class, 'jadw_id');
	}

	public function sis_audit_lks()
	{
		return $this->hasMany(SisAuditLks::class, 'jadw_id');
	}

	public function sis_audit_logbooks()
	{
		return $this->hasMany(SisAuditLogbook::class, 'jadw_id');
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

	public function sis_jadwal_tims()
	{
		return $this->hasMany(SisJadwalTim::class, 'jadw_id');
	}
}

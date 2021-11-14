<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisJadwalAudit
 * 
 * @property int $jadw_audit_id
 * @property int $jadw_id
 * @property string|null $jadw_audit_status
 * @property string|null $jadw_audit_status_komite
 * @property string|null $jadw_audit_jenis
 * @property int|null $mohon_id
 * @property int|null $sert_id
 * @property int|null $komodt_id
 * @property int|null $cust_sert_id
 * @property string|null $jadw_audit_nomor_sertifikat
 * @property string|null $jadw_audit_nomor_referensi
 * @property string|null $jadw_audit_kode_nace
 * @property string|null $jadw_audit_kode_ea
 * @property string|null $jadw_audit_ruang_lingkup
 * @property string|null $jadw_audit_standart_acuan
 * @property string|null $jadw_audit_kegiatan
 * @property string|null $jadw_audit_tujuan_audit
 * @property string|null $jadw_audit_sni
 * @property string|null $jadw_audit_merk
 * @property string|null $jadw_audit_tipe
 * @property string|null $jadw_audit_ukuran
 * @property string|null $jadw_audit_kapasitas_produksi_tahunan
 * @property string|null $jadw_audit_kapasitas_produksi_tahunan_satuan
 * @property string|null $jadw_audit_sertifikat_filepath
 * @property string|null $jadw_audit_sertifikat_nomor
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 * @property SisPermohonan|null $sis_permohonan
 * @property MasterKomoditi|null $master_komoditi
 * @property MasterSertifikasi|null $master_sertifikasi
 * @property SisPelangganSertifikasi|null $sis_pelanggan_sertifikasi
 * @property Collection|SisAuditLks[] $sis_audit_lks
 *
 * @package App\Models\BbkkpSis
 */
class SisJadwalAudit extends Model
{
	protected $table = 'sis_jadwal_audit';
	protected $primaryKey = 'jadw_audit_id';

	protected $casts = [
		'jadw_id' => 'int',
		'mohon_id' => 'int',
		'sert_id' => 'int',
		'komodt_id' => 'int',
		'cust_sert_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'jadw_audit_status',
		'jadw_audit_status_komite',
		'jadw_audit_jenis',
		'mohon_id',
		'sert_id',
		'komodt_id',
		'cust_sert_id',
		'jadw_audit_nomor_sertifikat',
		'jadw_audit_nomor_referensi',
		'jadw_audit_kode_nace',
		'jadw_audit_kode_ea',
		'jadw_audit_ruang_lingkup',
		'jadw_audit_standart_acuan',
		'jadw_audit_kegiatan',
		'jadw_audit_tujuan_audit',
		'jadw_audit_sni',
		'jadw_audit_merk',
		'jadw_audit_tipe',
		'jadw_audit_ukuran',
		'jadw_audit_kapasitas_produksi_tahunan',
		'jadw_audit_kapasitas_produksi_tahunan_satuan',
		'jadw_audit_sertifikat_filepath',
		'jadw_audit_sertifikat_nomor'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}

	public function master_komoditi()
	{
		return $this->belongsTo(MasterKomoditi::class, 'komodt_id');
	}

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}

	public function sis_pelanggan_sertifikasi()
	{
		return $this->belongsTo(SisPelangganSertifikasi::class, 'cust_sert_id');
	}

	public function sis_audit_lks()
	{
		return $this->hasMany(SisAuditLks::class, 'jadw_audit_id');
	}
}

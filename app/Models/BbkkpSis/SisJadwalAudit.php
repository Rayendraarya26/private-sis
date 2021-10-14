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
 * @property int|null $jadw_id
 * @property string|null $jadw_audit_jenis
 * @property int|null $mohon_id
 * @property int|null $sert_id
 * @property int|null $komodt_id
 * @property int|null $cust_sert_id
 * @property string|null $jadw_audit_nomor_referensi
 * @property string|null $jadw_audit_kode_nace
 * @property string|null $jadw_audit_standart_acuan
 * @property string|null $jadw_audit_ruang_lingkup
 * @property string|null $jadw_audit_kegiatan
 * @property string|null $jadw_audit_tujuan_audit
 * @property string|null $jadw_audit_sertifikat_status
 * @property string|null $jadw_audit_sertifikat_filepath
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal|null $sis_jadwal
 * @property SisPermohonan|null $sis_permohonan
 * @property Collection|SisAuditTahap1[] $sis_audit_tahap1s
 * @property Collection|SisJadwalTim[] $sis_jadwal_tims
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
		'jadw_audit_jenis',
		'mohon_id',
		'sert_id',
		'komodt_id',
		'cust_sert_id',
		'jadw_audit_nomor_referensi',
		'jadw_audit_kode_nace',
		'jadw_audit_standart_acuan',
		'jadw_audit_ruang_lingkup',
		'jadw_audit_kegiatan',
		'jadw_audit_tujuan_audit',
		'jadw_audit_sertifikat_status',
		'jadw_audit_sertifikat_filepath'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}

	public function sis_audit_tahap1s()
	{
		return $this->hasMany(SisAuditTahap1::class, 'jadw_audit_id');
	}

	public function sis_jadwal_tims()
	{
		return $this->hasMany(SisJadwalTim::class, 'jadw_audit_id');
	}
}

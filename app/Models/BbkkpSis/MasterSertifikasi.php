<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterSertifikasi
 * 
 * @property int $sert_id
 * @property string|null $sert_nama
 * @property string|null $sert_deskripsi
 * @property int|null $sert_expired
 * @property string|null $sert_kode
 * @property string|null $sert_format_referensi
 * @property string|null $sert_format_nomor_sertifikat
 * @property string|null $sert_sni
 * @property string|null $sert_is_product
 * @property string|null $sert_tahap1_jenis
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|MasterKlausulTahap1[] $master_klausul_tahap1s
 * @property Collection|MasterSertifikasiDokumen[] $master_sertifikasi_dokumens
 * @property Collection|MasterSertifikasiKlausul[] $master_sertifikasi_klausuls
 * @property Collection|PegawaiKompetensiAuditor[] $pegawai_kompetensi_auditors
 * @property Collection|PegawaiKompetensiKomite[] $pegawai_kompetensi_komites
 * @property Collection|SisJadwalAudit[] $sis_jadwal_audits
 * @property Collection|SisPelangganSertifikasi[] $sis_pelanggan_sertifikasis
 * @property Collection|SisPermohonanDetail[] $sis_permohonan_details
 *
 * @package App\Models\BbkkpSis
 */
class MasterSertifikasi extends Model
{
	protected $table = 'master_sertifikasi';
	protected $primaryKey = 'sert_id';

	protected $casts = [
		'sert_expired' => 'int'
	];

	protected $fillable = [
		'sert_nama',
		'sert_deskripsi',
		'sert_expired',
		'sert_kode',
		'sert_format_referensi',
		'sert_format_nomor_sertifikat',
		'sert_sni',
		'sert_is_product',
		'sert_tahap1_jenis'
	];

	public function master_klausul_tahap1s()
	{
		return $this->hasMany(MasterKlausulTahap1::class, 'sert_id');
	}

	public function master_sertifikasi_dokumens()
	{
		return $this->hasMany(MasterSertifikasiDokumen::class, 'sert_id');
	}

	public function master_sertifikasi_klausuls()
	{
		return $this->hasMany(MasterSertifikasiKlausul::class, 'sert_id');
	}

	public function pegawai_kompetensi_auditors()
	{
		return $this->hasMany(PegawaiKompetensiAuditor::class, 'sert_id');
	}

	public function pegawai_kompetensi_komites()
	{
		return $this->hasMany(PegawaiKompetensiKomite::class, 'sert_id');
	}

	public function sis_jadwal_audits()
	{
		return $this->hasMany(SisJadwalAudit::class, 'sert_id');
	}

	public function sis_pelanggan_sertifikasis()
	{
		return $this->hasMany(SisPelangganSertifikasi::class, 'sert_id');
	}

	public function sis_permohonan_details()
	{
		return $this->hasMany(SisPermohonanDetail::class, 'sert_id');
	}
}

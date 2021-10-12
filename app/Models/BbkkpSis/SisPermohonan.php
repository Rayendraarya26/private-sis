<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonan
 * 
 * @property int $mohon_id
 * @property int $cust_id
 * @property int $user_id
 * @property int $sert_id
 * @property string $mohon_approved_status
 * @property string $mohon_jenis_status
 * @property int|null $cust_sert_id
 * @property string|null $mohon_kajian_permohonan_file
 * @property string|null $mohon_pernyataan_persetujuan_file
 * @property string|null $mohon_harus_lunas_status
 * @property float|null $mohon_harga_permohonan
 * @property string|null $mohon_cust_email
 * @property string|null $mohon_cust_nomor_telp
 * @property string|null $mohon_cust_nomor_fax
 * @property string|null $mohon_cust_nomor_hp
 * @property string|null $mohon_cust_nama
 * @property int|null $jenis_perusahaan_id
 * @property int|null $badan_hukum_id
 * @property string|null $cust_asing
 * @property int|null $negara_id
 * @property int|null $kec_id
 * @property int|null $kab_id
 * @property int|null $prov_id
 * @property string|null $mohon_cust_alamat
 * @property string|null $mohon_cust_nomor_akta_pendirian
 * @property string|null $mohon_cust_nama_pemilik
 * @property string|null $mohon_cust_nama_pimpinan
 * @property string|null $mohon_cust_nama_wakil_manajemen
 * @property int|null $mohon_cust_jumlah_bagian
 * @property int|null $mohon_cust_jumlah_manajemen
 * @property int|null $mohon_cust_jumlah_administrasi
 * @property int|null $mohon_cust_jumlah_part_time
 * @property int|null $mohon_cust_jumlah_operasional
 * @property int|null $mohon_cust_jumlah_shift_1
 * @property int|null $mohon_cust_jumlah_shift_2
 * @property int|null $mohon_cust_jumlah_shift_3
 * @property int|null $mohon_cust_jumlah_non_permanen
 * @property int|null $mohon_cust_shif_kerja
 * @property string|null $mohon_cust_luas_tanah
 * @property string|null $mohon_cust_luas_bangunan
 * @property string|null $mohon_pertanyaan_filepath
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterSertifikasi $master_sertifikasi
 * @property SisPelanggan $sis_pelanggan
 * @property SysUser $sys_user
 * @property SisPelangganSertifikasi|null $sis_pelanggan_sertifikasi
 * @property MasterJenisPerusahaan|null $master_jenis_perusahaan
 * @property MasterBadanHukum|null $master_badan_hukum
 * @property MasterNegara|null $master_negara
 * @property MasterProvinsi|null $master_provinsi
 * @property MasterKabupaten|null $master_kabupaten
 * @property MasterKecamatan|null $master_kecamatan
 * @property Collection|SisBillingItems[] $sis_billing_items
 * @property Collection|SisJadwalAudit[] $sis_jadwal_audits
 * @property Collection|SisPermohonanDokumen[] $sis_permohonan_dokumens
 * @property Collection|SisPermohonanKomoditi[] $sis_permohonan_komoditis
 * @property Collection|SisPermohonanPabrik[] $sis_permohonan_pabriks
 * @property Collection|SisPermohonanStatus[] $sis_permohonan_statuses
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonan extends Model
{
	protected $table = 'sis_permohonan';
	protected $primaryKey = 'mohon_id';

	protected $casts = [
		'cust_id' => 'int',
		'user_id' => 'int',
		'sert_id' => 'int',
		'cust_sert_id' => 'int',
		'mohon_harga_permohonan' => 'float',
		'jenis_perusahaan_id' => 'int',
		'badan_hukum_id' => 'int',
		'negara_id' => 'int',
		'kec_id' => 'int',
		'kab_id' => 'int',
		'prov_id' => 'int',
		'mohon_cust_jumlah_bagian' => 'int',
		'mohon_cust_jumlah_manajemen' => 'int',
		'mohon_cust_jumlah_administrasi' => 'int',
		'mohon_cust_jumlah_part_time' => 'int',
		'mohon_cust_jumlah_operasional' => 'int',
		'mohon_cust_jumlah_shift_1' => 'int',
		'mohon_cust_jumlah_shift_2' => 'int',
		'mohon_cust_jumlah_shift_3' => 'int',
		'mohon_cust_jumlah_non_permanen' => 'int',
		'mohon_cust_shif_kerja' => 'int'
	];

	protected $fillable = [
		'cust_id',
		'user_id',
		'sert_id',
		'mohon_approved_status',
		'mohon_jenis_status',
		'cust_sert_id',
		'mohon_kajian_permohonan_file',
		'mohon_pernyataan_persetujuan_file',
		'mohon_harus_lunas_status',
		'mohon_harga_permohonan',
		'mohon_cust_email',
		'mohon_cust_nomor_telp',
		'mohon_cust_nomor_fax',
		'mohon_cust_nomor_hp',
		'mohon_cust_nama',
		'jenis_perusahaan_id',
		'badan_hukum_id',
		'cust_asing',
		'negara_id',
		'kec_id',
		'kab_id',
		'prov_id',
		'mohon_cust_alamat',
		'mohon_cust_nomor_akta_pendirian',
		'mohon_cust_nama_pemilik',
		'mohon_cust_nama_pimpinan',
		'mohon_cust_nama_wakil_manajemen',
		'mohon_cust_jumlah_bagian',
		'mohon_cust_jumlah_manajemen',
		'mohon_cust_jumlah_administrasi',
		'mohon_cust_jumlah_part_time',
		'mohon_cust_jumlah_operasional',
		'mohon_cust_jumlah_shift_1',
		'mohon_cust_jumlah_shift_2',
		'mohon_cust_jumlah_shift_3',
		'mohon_cust_jumlah_non_permanen',
		'mohon_cust_shif_kerja',
		'mohon_cust_luas_tanah',
		'mohon_cust_luas_bangunan',
		'mohon_pertanyaan_filepath'
	];

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}

	public function sys_user()
	{
		return $this->belongsTo(SysUser::class, 'user_id');
	}

	public function sis_pelanggan_sertifikasi()
	{
		return $this->belongsTo(SisPelangganSertifikasi::class, 'cust_sert_id');
	}

	public function master_jenis_perusahaan()
	{
		return $this->belongsTo(MasterJenisPerusahaan::class, 'jenis_perusahaan_id');
	}

	public function master_badan_hukum()
	{
		return $this->belongsTo(MasterBadanHukum::class, 'badan_hukum_id');
	}

	public function master_negara()
	{
		return $this->belongsTo(MasterNegara::class, 'negara_id');
	}

	public function master_provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'prov_id');
	}

	public function master_kabupaten()
	{
		return $this->belongsTo(MasterKabupaten::class, 'kab_id');
	}

	public function master_kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'kec_id');
	}

	public function sis_billing_items()
	{
		return $this->hasMany(SisBillingItems::class, 'mohon_id');
	}

	public function sis_jadwal_audits()
	{
		return $this->hasMany(SisJadwalAudit::class, 'mohon_id');
	}

	public function sis_permohonan_dokumens()
	{
		return $this->hasMany(SisPermohonanDokumen::class, 'mohon_id');
	}

	public function sis_permohonan_komoditis()
	{
		return $this->hasMany(SisPermohonanKomoditi::class, 'mohon_id');
	}

	public function sis_permohonan_pabriks()
	{
		return $this->hasMany(SisPermohonanPabrik::class, 'mohon_id');
	}

	public function sis_permohonan_statuses()
	{
		return $this->hasMany(SisPermohonanStatus::class, 'status_mohon_id');
	}
}

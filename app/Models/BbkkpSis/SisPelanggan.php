<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPelanggan
 * 
 * @property int $cust_id
 * @property int $user_id
 * @property string|null $cust_email
 * @property string|null $cust_nomor_telp
 * @property string|null $cust_nomor_fax
 * @property string|null $cust_nomor_hp
 * @property string|null $cust_nama
 * @property int|null $jenis_perusahaan_id
 * @property int|null $badan_hukum_id
 * @property string|null $cust_asing
 * @property int|null $negara_id
 * @property int|null $kec_id
 * @property int|null $kab_id
 * @property int|null $prov_id
 * @property string|null $cust_alamat
 * @property string|null $cust_nomor_akta_pendirian
 * @property string|null $cust_nama_pemilik
 * @property string|null $cust_nama_pimpinan
 * @property string|null $cust_nama_wakil_manajemen
 * @property int|null $cust_jumlah_bagian
 * @property int|null $cust_jumlah_manajemen
 * @property int|null $cust_jumlah_administrasi
 * @property int|null $cust_jumlah_part_time
 * @property int|null $cust_jumlah_operasional
 * @property int|null $cust_jumlah_shift_1
 * @property int|null $cust_jumlah_shift_2
 * @property int|null $cust_jumlah_shift_3
 * @property int|null $cust_jumlah_non_permanen
 * @property int|null $cust_shif_kerja
 * @property string|null $cust_luas_tanah
 * @property string|null $cust_luas_bangunan
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterBadanHukum|null $master_badan_hukum
 * @property MasterJenisPerusahaan|null $master_jenis_perusahaan
 * @property MasterKabupaten|null $master_kabupaten
 * @property MasterKecamatan|null $master_kecamatan
 * @property MasterProvinsi|null $master_provinsi
 * @property SysUser $sys_user
 * @property MasterNegara|null $master_negara
 * @property Collection|SisBilling[] $sis_billings
 * @property Collection|SisJadwal[] $sis_jadwals
 * @property Collection|SisPelangganDokumen[] $sis_pelanggan_dokumens
 * @property Collection|SisPelangganPabrik[] $sis_pelanggan_pabriks
 * @property Collection|SisPelangganSertifikasi[] $sis_pelanggan_sertifikasis
 * @property Collection|SisPermohonan[] $sis_permohonans
 *
 * @package App\Models\BbkkpSis
 */
class SisPelanggan extends Model
{
	protected $table = 'sis_pelanggan';
	protected $primaryKey = 'cust_id';

	protected $casts = [
		'user_id' => 'int',
		'jenis_perusahaan_id' => 'int',
		'badan_hukum_id' => 'int',
		'negara_id' => 'int',
		'kec_id' => 'int',
		'kab_id' => 'int',
		'prov_id' => 'int',
		'cust_jumlah_bagian' => 'int',
		'cust_jumlah_manajemen' => 'int',
		'cust_jumlah_administrasi' => 'int',
		'cust_jumlah_part_time' => 'int',
		'cust_jumlah_operasional' => 'int',
		'cust_jumlah_shift_1' => 'int',
		'cust_jumlah_shift_2' => 'int',
		'cust_jumlah_shift_3' => 'int',
		'cust_jumlah_non_permanen' => 'int',
		'cust_shif_kerja' => 'int'
	];

	protected $fillable = [
		'user_id',
		'cust_email',
		'cust_nomor_telp',
		'cust_nomor_fax',
		'cust_nomor_hp',
		'cust_nama',
		'jenis_perusahaan_id',
		'badan_hukum_id',
		'cust_asing',
		'negara_id',
		'kec_id',
		'kab_id',
		'prov_id',
		'cust_alamat',
		'cust_nomor_akta_pendirian',
		'cust_nama_pemilik',
		'cust_nama_pimpinan',
		'cust_nama_wakil_manajemen',
		'cust_jumlah_bagian',
		'cust_jumlah_manajemen',
		'cust_jumlah_administrasi',
		'cust_jumlah_part_time',
		'cust_jumlah_operasional',
		'cust_jumlah_shift_1',
		'cust_jumlah_shift_2',
		'cust_jumlah_shift_3',
		'cust_jumlah_non_permanen',
		'cust_shif_kerja',
		'cust_luas_tanah',
		'cust_luas_bangunan'
	];

	public function master_badan_hukum()
	{
		return $this->belongsTo(MasterBadanHukum::class, 'badan_hukum_id');
	}

	public function master_jenis_perusahaan()
	{
		return $this->belongsTo(MasterJenisPerusahaan::class, 'jenis_perusahaan_id');
	}

	public function master_kabupaten()
	{
		return $this->belongsTo(MasterKabupaten::class, 'kab_id');
	}

	public function master_kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'kec_id');
	}

	public function master_provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'prov_id');
	}

	public function sys_user()
	{
		return $this->belongsTo(SysUser::class, 'user_id');
	}

	public function master_negara()
	{
		return $this->belongsTo(MasterNegara::class, 'negara_id');
	}

	public function sis_billings()
	{
		return $this->hasMany(SisBilling::class, 'cust_id');
	}

	public function sis_jadwals()
	{
		return $this->hasMany(SisJadwal::class, 'cust_id');
	}

	public function sis_pelanggan_dokumens()
	{
		return $this->hasMany(SisPelangganDokumen::class, 'cust_id');
	}

	public function sis_pelanggan_pabriks()
	{
		return $this->hasMany(SisPelangganPabrik::class, 'cust_id');
	}

	public function sis_pelanggan_sertifikasis()
	{
		return $this->hasMany(SisPelangganSertifikasi::class, 'cust_id');
	}

	public function sis_permohonans()
	{
		return $this->hasMany(SisPermohonan::class, 'cust_id');
	}
}

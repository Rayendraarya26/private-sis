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
 * @property int|null $reg_id
 * @property int $user_id
 * @property string|null $cust_email
 * @property string|null $cust_nomor_telp
 * @property string|null $cust_nomor_fax
 * @property string|null $cust_nomor_hp
 * @property string|null $cust_nama
 * @property int|null $jenis_perusahaan_id
 * @property int|null $badan_hukum_id
 * @property int|null $kec_id
 * @property int|null $kab_id
 * @property int|null $prov_id
 * @property string|null $cust_alamat
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterBadanHukum|null $master_badan_hukum
 * @property MasterJenisPerusahaan|null $master_jenis_perusahaan
 * @property MasterKabupaten|null $master_kabupaten
 * @property MasterKecamatan|null $master_kecamatan
 * @property MasterProvinsi|null $master_provinsi
 * @property SisRegisterPelanggan|null $sis_register_pelanggan
 * @property SysUser $sys_user
 * @property Collection|SisPelangganDokuman[] $sis_pelanggan_dokumen
 * @property Collection|SisPelangganSertifikasi[] $sis_pelanggan_sertifikasis
 *
 * @package App\Models\BbkkpSis
 */
class SisPelanggan extends Model
{
	protected $table = 'sis_pelanggan';
	protected $primaryKey = 'cust_id';

	protected $casts = [
		'reg_id' => 'int',
		'user_id' => 'int',
		'jenis_perusahaan_id' => 'int',
		'badan_hukum_id' => 'int',
		'kec_id' => 'int',
		'kab_id' => 'int',
		'prov_id' => 'int'
	];

	protected $fillable = [
		'reg_id',
		'user_id',
		'cust_email',
		'cust_nomor_telp',
		'cust_nomor_fax',
		'cust_nomor_hp',
		'cust_nama',
		'jenis_perusahaan_id',
		'badan_hukum_id',
		'kec_id',
		'kab_id',
		'prov_id',
		'cust_alamat'
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

	public function sis_register_pelanggan()
	{
		return $this->belongsTo(SisRegisterPelanggan::class, 'reg_id');
	}

	public function sys_user()
	{
		return $this->belongsTo(SysUser::class, 'user_id');
	}

	public function sis_pelanggan_dokumen()
	{
		return $this->hasMany(SisPelangganDokuman::class, 'cust_id');
	}

	public function sis_pelanggan_sertifikasis()
	{
		return $this->hasMany(SisPelangganSertifikasi::class, 'cust_id');
	}
}

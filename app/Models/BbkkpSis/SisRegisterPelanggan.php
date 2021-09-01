<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisRegisterPelanggan
 * 
 * @property int $reg_id
 * @property string|null $reg_email
 * @property string|null $reg_nomor_telp
 * @property string|null $reg_nomor_fax
 * @property string|null $reg_nomor_hp
 * @property string|null $reg_nama
 * @property int|null $jenis_perusahaan_id
 * @property int|null $badan_hukum_id
 * @property int|null $kec_id
 * @property int|null $kab_id
 * @property int|null $prov_id
 * @property string|null $reg_alamat
 * @property string $reg_status
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterBadanHukum|null $master_badan_hukum
 * @property MasterJenisPerusahaan|null $master_jenis_perusahaan
 * @property MasterKabupaten|null $master_kabupaten
 * @property MasterKecamatan|null $master_kecamatan
 * @property MasterProvinsi|null $master_provinsi
 * @property Collection|SisPelanggan[] $sis_pelanggans
 *
 * @package App\Models\BbkkpSis
 */
class SisRegisterPelanggan extends Model
{
	protected $table = 'sis_register_pelanggan';
	protected $primaryKey = 'reg_id';

	protected $casts = [
		'jenis_perusahaan_id' => 'int',
		'badan_hukum_id' => 'int',
		'kec_id' => 'int',
		'kab_id' => 'int',
		'prov_id' => 'int'
	];

	protected $fillable = [
		'reg_email',
		'reg_nomor_telp',
		'reg_nomor_fax',
		'reg_nomor_hp',
		'reg_nama',
		'jenis_perusahaan_id',
		'badan_hukum_id',
		'kec_id',
		'kab_id',
		'prov_id',
		'reg_alamat',
		'reg_status'
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

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'reg_id');
	}
}

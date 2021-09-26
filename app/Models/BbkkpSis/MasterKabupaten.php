<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKabupaten
 * 
 * @property int $kab_id
 * @property int $prov_id
 * @property string $kab_nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property MasterProvinsi $master_provinsi
 * @property Collection|MasterKecamatan[] $master_kecamatans
 * @property Collection|SisPelanggan[] $sis_pelanggans
 * @property Collection|SisPelangganPabrik[] $sis_pelanggan_pabriks
 * @property Collection|SisPermohonanPabrik[] $sis_permohonan_pabriks
 *
 * @package App\Models\BbkkpSis
 */
class MasterKabupaten extends Model
{
	protected $table = 'master_kabupaten';
	protected $primaryKey = 'kab_id';
	public $incrementing = false;

	protected $casts = [
		'kab_id' => 'int',
		'prov_id' => 'int'
	];

	protected $fillable = [
		'prov_id',
		'kab_nama'
	];

	public function master_provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'prov_id');
	}

	public function master_kecamatans()
	{
		return $this->hasMany(MasterKecamatan::class, 'kab_id');
	}

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'kab_id');
	}

	public function sis_pelanggan_pabriks()
	{
		return $this->hasMany(SisPelangganPabrik::class, 'kab_id');
	}

	public function sis_permohonan_pabriks()
	{
		return $this->hasMany(SisPermohonanPabrik::class, 'kab_id');
	}
}

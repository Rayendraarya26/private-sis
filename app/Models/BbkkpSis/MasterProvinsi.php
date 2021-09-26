<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterProvinsi
 * 
 * @property int $prov_id
 * @property string $prov_nama
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|MasterKabupaten[] $master_kabupatens
 * @property Collection|SisPelanggan[] $sis_pelanggans
 * @property Collection|SisPelangganPabrik[] $sis_pelanggan_pabriks
 * @property Collection|SisPermohonanPabrik[] $sis_permohonan_pabriks
 *
 * @package App\Models\BbkkpSis
 */
class MasterProvinsi extends Model
{
	protected $table = 'master_provinsi';
	protected $primaryKey = 'prov_id';

	protected $fillable = [
		'prov_nama'
	];

	public function master_kabupatens()
	{
		return $this->hasMany(MasterKabupaten::class, 'prov_id');
	}

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'prov_id');
	}

	public function sis_pelanggan_pabriks()
	{
		return $this->hasMany(SisPelangganPabrik::class, 'prov_id');
	}

	public function sis_permohonan_pabriks()
	{
		return $this->hasMany(SisPermohonanPabrik::class, 'prov_id');
	}
}

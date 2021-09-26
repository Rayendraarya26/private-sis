<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKecamatan
 * 
 * @property int $kec_id
 * @property int $kab_id
 * @property string $kec_nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property MasterKabupaten $master_kabupaten
 * @property MasterDesa $master_desa
 * @property Collection|SisPelanggan[] $sis_pelanggans
 * @property Collection|SisPelangganPabrik[] $sis_pelanggan_pabriks
 * @property Collection|SisPermohonanPabrik[] $sis_permohonan_pabriks
 *
 * @package App\Models\BbkkpSis
 */
class MasterKecamatan extends Model
{
	protected $table = 'master_kecamatan';
	protected $primaryKey = 'kec_id';

	protected $casts = [
		'kab_id' => 'int'
	];

	protected $fillable = [
		'kab_id',
		'kec_nama'
	];

	public function master_kabupaten()
	{
		return $this->belongsTo(MasterKabupaten::class, 'kab_id');
	}

	public function master_desa()
	{
		return $this->hasOne(MasterDesa::class, 'kec_id');
	}

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'kec_id');
	}

	public function sis_pelanggan_pabriks()
	{
		return $this->hasMany(SisPelangganPabrik::class, 'kec_id');
	}

	public function sis_permohonan_pabriks()
	{
		return $this->hasMany(SisPermohonanPabrik::class, 'kec_id');
	}
}

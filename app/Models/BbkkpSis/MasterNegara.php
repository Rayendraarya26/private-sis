<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterNegara
 * 
 * @property int $negara_id
 * @property string|null $negara_kode
 * @property string|null $negara_nama
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|SisPelanggan[] $sis_pelanggans
 * @property Collection|SisPelangganPabrik[] $sis_pelanggan_pabriks
 * @property Collection|SisPermohonan[] $sis_permohonans
 * @property Collection|SisPermohonanPabrik[] $sis_permohonan_pabriks
 *
 * @package App\Models\BbkkpSis
 */
class MasterNegara extends Model
{
	protected $table = 'master_negara';
	protected $primaryKey = 'negara_id';

	protected $fillable = [
		'negara_kode',
		'negara_nama'
	];

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'negara_id');
	}

	public function sis_pelanggan_pabriks()
	{
		return $this->hasMany(SisPelangganPabrik::class, 'negara_id');
	}

	public function sis_permohonans()
	{
		return $this->hasMany(SisPermohonan::class, 'negara_id');
	}

	public function sis_permohonan_pabriks()
	{
		return $this->hasMany(SisPermohonanPabrik::class, 'negara_id');
	}
}

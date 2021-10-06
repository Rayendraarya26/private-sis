<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterBadanHukum
 * 
 * @property int $badan_hukum_id
 * @property string|null $badan_hukum_nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|SisPelanggan[] $sis_pelanggans
 * @property Collection|SisPermohonan[] $sis_permohonans
 *
 * @package App\Models\BbkkpSis
 */
class MasterBadanHukum extends Model
{
	protected $table = 'master_badan_hukum';
	protected $primaryKey = 'badan_hukum_id';

	protected $fillable = [
		'badan_hukum_nama'
	];

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'badan_hukum_id');
	}

	public function sis_permohonans()
	{
		return $this->hasMany(SisPermohonan::class, 'badan_hukum_id');
	}
}

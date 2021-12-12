<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterJenisPerusahaan
 * 
 * @property int $jenis_perusahaan_id
 * @property string|null $jenis_perusahaan_nama
 * @property string|null $jenis_perusahaan_deskripsi
 * @property string|null $jenis_perusahaan_color
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|SisPelanggan[] $sis_pelanggans
 * @property Collection|SisPermohonan[] $sis_permohonans
 *
 * @package App\Models\BbkkpSis
 */
class MasterJenisPerusahaan extends Model
{
	protected $table = 'master_jenis_perusahaan';
	protected $primaryKey = 'jenis_perusahaan_id';

	protected $fillable = [
		'jenis_perusahaan_nama',
		'jenis_perusahaan_deskripsi',
		'jenis_perusahaan_color'
	];

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'jenis_perusahaan_id');
	}

	public function sis_permohonans()
	{
		return $this->hasMany(SisPermohonan::class, 'jenis_perusahaan_id');
	}
}

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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|SisPelanggan[] $sis_pelanggans
 *
 * @package App\Models\BbkkpSis
 */
class MasterJenisPerusahaan extends Model
{
	protected $table = 'master_jenis_perusahaan';
	protected $primaryKey = 'jenis_perusahaan_id';

	protected $fillable = [
		'jenis_perusahaan_nama',
		'jenis_perusahaan_deskripsi'
	];

	public function sis_pelanggans()
	{
		return $this->hasMany(SisPelanggan::class, 'jenis_perusahaan_id');
	}
}

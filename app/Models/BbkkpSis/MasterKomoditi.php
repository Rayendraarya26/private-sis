<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKomoditi
 * 
 * @property int $komodt_id
 * @property string|null $komodt_nama
 * @property string|null $komodt_sni
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|SisPelangganSertifikasi[] $sis_pelanggan_sertifikasis
 * @property Collection|SisPermohonanKomoditi[] $sis_permohonan_komoditis
 *
 * @package App\Models\BbkkpSis
 */
class MasterKomoditi extends Model
{
	protected $table = 'master_komoditi';
	protected $primaryKey = 'komodt_id';

	protected $fillable = [
		'komodt_nama',
		'komodt_sni'
	];

	public function sis_pelanggan_sertifikasis()
	{
		return $this->hasMany(SisPelangganSertifikasi::class, 'komodt_id');
	}

	public function sis_permohonan_komoditis()
	{
		return $this->hasMany(SisPermohonanKomoditi::class, 'komodt_id');
	}
}

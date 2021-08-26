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
}

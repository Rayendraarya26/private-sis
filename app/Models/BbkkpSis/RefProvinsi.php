<?php


namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefProvinsi
 *
 * @property int $prov_id
 * @property string $prov_nama
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 *
 * @property Collection|RefKabupaten[] $ref_kabupatens
 *
 * @package App\Models\BbkkpSis
 */
class RefProvinsi extends Model
{
	protected $table = 'ref_provinsi';
	protected $primaryKey = 'prov_id';

	protected $fillable = [
		'prov_nama'
	];

	public function ref_kabupatens()
	{
		return $this->hasMany(RefKabupaten::class, 'prov_id');
	}
}

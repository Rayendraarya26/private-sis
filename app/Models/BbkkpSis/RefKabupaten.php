<?php


namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKabupaten
 *
 * @property int $kab_id
 * @property int $prov_id
 * @property string $kab_nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property RefProvinsi $ref_provinsi
 * @property Collection|RefKecamatan[] $ref_kecamatans
 *
 * @package App\Models\BbkkpSis
 */
class RefKabupaten extends Model
{
	protected $table = 'ref_kabupaten';
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

	public function ref_provinsi()
	{
		return $this->belongsTo(RefProvinsi::class, 'prov_id');
	}

	public function ref_kecamatans()
	{
		return $this->hasMany(RefKecamatan::class, 'kab_id');
	}
}

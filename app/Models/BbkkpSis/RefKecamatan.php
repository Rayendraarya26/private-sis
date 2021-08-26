<?php


namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKecamatan
 *
 * @property int $kec_id
 * @property int $kab_id
 * @property string $kec_nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property RefKabupaten $ref_kabupaten
 *
 * @package App\Models\BbkkpSis
 */
class RefKecamatan extends Model
{
	protected $table = 'ref_kecamatan';
	protected $primaryKey = 'kec_id';

	protected $casts = [
		'kab_id' => 'int'
	];

	protected $fillable = [
		'kab_id',
		'kec_nama'
	];

	public function ref_kabupaten()
	{
		return $this->belongsTo(RefKabupaten::class, 'kab_id');
	}
}

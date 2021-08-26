<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
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
}

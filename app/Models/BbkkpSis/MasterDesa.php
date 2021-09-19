<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterDesa
 * 
 * @property int|null $des_id
 * @property int|null $kec_id
 * @property string|null $des_nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property MasterKecamatan|null $master_kecamatan
 *
 * @package App\Models\BbkkpSis
 */
class MasterDesa extends Model
{
	protected $table = 'master_desa';
	public $incrementing = false;

	protected $casts = [
		'des_id' => 'int',
		'kec_id' => 'int'
	];

	protected $fillable = [
		'des_id',
		'kec_id',
		'des_nama'
	];

	public function master_kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'kec_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanStatus
 * 
 * @property int|null $status_id
 * @property int|null $status_mohon_id
 * @property string|null $status_tipe
 * @property string|null $status_judul
 * @property string|null $status_pesan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisPermohonan|null $sis_permohonan
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanStatus extends Model
{
	protected $table = 'sis_permohonan_status';
	public $incrementing = false;

	protected $casts = [
		'status_id' => 'int',
		'status_mohon_id' => 'int'
	];

	protected $fillable = [
		'status_id',
		'status_mohon_id',
		'status_tipe',
		'status_judul',
		'status_pesan'
	];

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'status_mohon_id');
	}
}

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
 * @property int $status_id
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
	protected $primaryKey = 'status_id';

	protected $casts = [
		'status_mohon_id' => 'int'
	];

	protected $fillable = [
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

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLapRingkas
 * 
 * @property int $lap_ringkas_id
 * @property int $jadw_id
 * @property string|null $lap_ringkas_kesimpulan
 * @property string|null $lap_ringkas_rekomendasi
 * @property string|null $lap_ringkas_filepath
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLapRingkas extends Model
{
	protected $table = 'sis_audit_lap_ringkas';
	protected $primaryKey = 'lap_ringkas_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'lap_ringkas_kesimpulan',
		'lap_ringkas_rekomendasi',
		'lap_ringkas_filepath'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

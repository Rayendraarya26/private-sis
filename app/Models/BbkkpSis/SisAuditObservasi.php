<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditObservasi
 * 
 * @property int $obsvasi_id
 * @property int $jadw_id
 * @property string|null $obsvasi_uraian
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditObservasi extends Model
{
	protected $table = 'sis_audit_observasi';
	protected $primaryKey = 'obsvasi_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'obsvasi_uraian'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

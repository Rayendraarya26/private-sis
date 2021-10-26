<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLogbook
 * 
 * @property int $logbook_id
 * @property int $jadw_tim_id
 * @property string|null $logbook_filepath
 * @property string|null $logbook_jenis
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwalTim $sis_jadwal_tim
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLogbook extends Model
{
	protected $table = 'sis_audit_logbook';
	protected $primaryKey = 'logbook_id';

	protected $casts = [
		'jadw_tim_id' => 'int'
	];

	protected $fillable = [
		'jadw_tim_id',
		'logbook_filepath',
		'logbook_jenis'
	];

	public function sis_jadwal_tim()
	{
		return $this->belongsTo(SisJadwalTim::class, 'jadw_tim_id');
	}
}

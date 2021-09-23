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
 * @property int $jadw_id
 * @property string|null $logbook_filepath
 * @property string|null $logbook_jenis
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLogbook extends Model
{
	protected $table = 'sis_audit_logbook';
	protected $primaryKey = 'logbook_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'logbook_filepath',
		'logbook_jenis'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

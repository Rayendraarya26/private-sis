<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisJadwalLog
 * 
 * @property int $jlog_id
 * @property int|null $jadw_id
 * @property int|null $jadw_audit_id
 * @property string|null $jlog_tipe
 * @property string|null $jlog_judul
 * @property string|null $jlog_pesan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal|null $sis_jadwal
 * @property SisJadwalAudit|null $sis_jadwal_audit
 *
 * @package App\Models\BbkkpSis
 */
class SisJadwalLog extends Model
{
	protected $table = 'sis_jadwal_log';
	protected $primaryKey = 'jlog_id';

	protected $casts = [
		'jadw_id' => 'int',
		'jadw_audit_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'jadw_audit_id',
		'jlog_tipe',
		'jlog_judul',
		'jlog_pesan'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}

	public function sis_jadwal_audit()
	{
		return $this->belongsTo(SisJadwalAudit::class, 'jadw_audit_id');
	}
}

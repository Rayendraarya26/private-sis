<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1Logbook
 * 
 * @property int $thp1_logbook_id
 * @property int|null $thp1_tim_id
 * @property string|null $thp1_logbook_filepath
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditTahap1Tim|null $sis_audit_tahap1_tim
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1Logbook extends Model
{
	protected $table = 'sis_audit_tahap1_logbook';
	protected $primaryKey = 'thp1_logbook_id';

	protected $casts = [
		'thp1_tim_id' => 'int'
	];

	protected $fillable = [
		'thp1_tim_id',
		'thp1_logbook_filepath'
	];

	public function sis_audit_tahap1_tim()
	{
		return $this->belongsTo(SisAuditTahap1Tim::class, 'thp1_tim_id');
	}
}

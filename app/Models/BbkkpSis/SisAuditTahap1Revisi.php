<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1Revisi
 * 
 * @property int $thp1_revisi_id
 * @property int $aud_thp1_id
 * @property string|null $thp1_revisi_text
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditTahap1 $sis_audit_tahap1
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1Revisi extends Model
{
	protected $table = 'sis_audit_tahap1_revisi';
	protected $primaryKey = 'thp1_revisi_id';

	protected $casts = [
		'aud_thp1_id' => 'int'
	];

	protected $fillable = [
		'aud_thp1_id',
		'thp1_revisi_text'
	];

	public function sis_audit_tahap1()
	{
		return $this->belongsTo(SisAuditTahap1::class, 'aud_thp1_id');
	}
}

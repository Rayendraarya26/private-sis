<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1RevisiFile
 * 
 * @property int $thp1_revisi_file_id
 * @property int|null $thp1_revisi_id
 * @property string|null $thp1_revisi_file_path
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditTahap1Revisi|null $sis_audit_tahap1_revisi
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1RevisiFile extends Model
{
	protected $table = 'sis_audit_tahap1_revisi_file';
	protected $primaryKey = 'thp1_revisi_file_id';

	protected $casts = [
		'thp1_revisi_id' => 'int'
	];

	protected $fillable = [
		'thp1_revisi_id',
		'thp1_revisi_file_path'
	];

	public function sis_audit_tahap1_revisi()
	{
		return $this->belongsTo(SisAuditTahap1Revisi::class, 'thp1_revisi_id');
	}
}

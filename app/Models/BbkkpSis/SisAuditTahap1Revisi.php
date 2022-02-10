<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1Revisi
 * 
 * @property int $thp1_revisi_id
 * @property int $aud_thp1_det_id
 * @property string|null $thp1_revisi_text
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditTahap1Detail $sis_audit_tahap1_detail
 * @property Collection|SisAuditTahap1RevisiFile[] $sis_audit_tahap1_revisi_files
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1Revisi extends Model
{
	protected $table = 'sis_audit_tahap1_revisi';
	protected $primaryKey = 'thp1_revisi_id';

	protected $casts = [
		'aud_thp1_det_id' => 'int'
	];

	protected $fillable = [
		'aud_thp1_det_id',
		'thp1_revisi_text'
	];

	public function sis_audit_tahap1_detail()
	{
		return $this->belongsTo(SisAuditTahap1Detail::class, 'aud_thp1_det_id');
	}

	public function sis_audit_tahap1_revisi_files()
	{
		return $this->hasMany(SisAuditTahap1RevisiFile::class, 'thp1_revisi_id');
	}
}

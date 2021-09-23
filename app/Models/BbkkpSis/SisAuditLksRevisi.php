<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLksRevisi
 * 
 * @property int $lks_revisi_id
 * @property int $lks_id
 * @property string|null $lks_revisi_catatan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditLk $sis_audit_lk
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLksRevisi extends Model
{
	protected $table = 'sis_audit_lks_revisi';
	protected $primaryKey = 'lks_revisi_id';
	public $incrementing = false;

	protected $casts = [
		'lks_revisi_id' => 'int',
		'lks_id' => 'int'
	];

	protected $fillable = [
		'lks_id',
		'lks_revisi_catatan'
	];

	public function sis_audit_lk()
	{
		return $this->belongsTo(SisAuditLk::class, 'lks_id');
	}
}

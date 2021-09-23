<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLksFile
 * 
 * @property int $lks_file_id
 * @property int $lks_id
 * @property string|null $lks_filepath
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisAuditLk $sis_audit_lk
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLksFile extends Model
{
	protected $table = 'sis_audit_lks_file';
	protected $primaryKey = 'lks_file_id';

	protected $casts = [
		'lks_id' => 'int'
	];

	protected $fillable = [
		'lks_id',
		'lks_filepath'
	];

	public function sis_audit_lk()
	{
		return $this->belongsTo(SisAuditLk::class, 'lks_id');
	}
}

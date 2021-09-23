<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditPpc
 * 
 * @property int $audit_ppc_id
 * @property int $jadw_id
 * @property string|null $audit_ppc_jenis_file
 * @property string|null $audit_ppc_filepath
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditPpc extends Model
{
	protected $table = 'sis_audit_ppc';
	protected $primaryKey = 'audit_ppc_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'audit_ppc_jenis_file',
		'audit_ppc_filepath'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

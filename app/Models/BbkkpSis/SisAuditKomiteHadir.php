<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditKomiteHadir
 * 
 * @property int $hadir_komte_id
 * @property int $rekmd_komte_id
 * @property int $komite_id
 * @property string $hadir_komte_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditKomiteHadir extends Model
{
	protected $table = 'sis_audit_komite_hadir';
	protected $primaryKey = 'hadir_komte_id';

	protected $casts = [
		'rekmd_komte_id' => 'int',
		'komite_id' => 'int'
	];

	protected $fillable = [
		'rekmd_komte_id',
		'komite_id',
		'hadir_komte_status'
	];
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditKomiteRekomendasi
 * 
 * @property int $rekmd_komte_id
 * @property int $jadw_id
 * @property string|null $rekmd_komte_isi
 * @property string|null $rekmd_komte_kronologin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditKomiteRekomendasi extends Model
{
	protected $table = 'sis_audit_komite_rekomendasi';
	protected $primaryKey = 'rekmd_komte_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'rekmd_komte_isi',
		'rekmd_komte_kronologin'
	];
}

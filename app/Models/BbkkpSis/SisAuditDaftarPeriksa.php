<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditDaftarPeriksa
 * 
 * @property int $dftr_periksa_id
 * @property int $jadw_id
 * @property string|null $dftr_periksa_file
 * @property string|null $dftr_periksa_oleh
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditDaftarPeriksa extends Model
{
	protected $table = 'sis_audit_daftar_periksa';
	protected $primaryKey = 'dftr_periksa_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'dftr_periksa_file',
		'dftr_periksa_oleh'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

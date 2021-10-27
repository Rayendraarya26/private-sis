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
 * @property int $jadw_tim_id
 * @property string|null $dftr_periksa_file
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwalTim $sis_jadwal_tim
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditDaftarPeriksa extends Model
{
	protected $table = 'sis_audit_daftar_periksa';
	protected $primaryKey = 'dftr_periksa_id';

	protected $casts = [
		'jadw_tim_id' => 'int'
	];

	protected $fillable = [
		'jadw_tim_id',
		'dftr_periksa_file'
	];

	public function sis_jadwal_tim()
	{
		return $this->belongsTo(SisJadwalTim::class, 'jadw_tim_id');
	}
}

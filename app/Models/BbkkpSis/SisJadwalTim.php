<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisJadwalTim
 * 
 * @property int $jadw_tim_id
 * @property int|null $jadw_audit_id
 * @property int $peg_id
 * @property string|null $jadw_tim_kode
 * @property string|null $jadw_tim_posisi
 * @property string|null $jadw_tim_kesanggupan
 * @property Carbon|null $jadw_tim_kesanggupan_tgl
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterPegawai $master_pegawai
 * @property SisJadwalAudit|null $sis_jadwal_audit
 *
 * @package App\Models\BbkkpSis
 */
class SisJadwalTim extends Model
{
	protected $table = 'sis_jadwal_tim';
	protected $primaryKey = 'jadw_tim_id';

	protected $casts = [
		'jadw_audit_id' => 'int',
		'peg_id' => 'int'
	];

	protected $dates = [
		'jadw_tim_kesanggupan_tgl'
	];

	protected $fillable = [
		'jadw_audit_id',
		'peg_id',
		'jadw_tim_kode',
		'jadw_tim_posisi',
		'jadw_tim_kesanggupan',
		'jadw_tim_kesanggupan_tgl'
	];

	public function master_pegawai()
	{
		return $this->belongsTo(MasterPegawai::class, 'peg_id');
	}

	public function sis_jadwal_audit()
	{
		return $this->belongsTo(SisJadwalAudit::class, 'jadw_audit_id');
	}
}

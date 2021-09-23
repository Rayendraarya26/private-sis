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
 * @property int $jadw_id
 * @property int $peg_id
 * @property string|null $jadw_tim_kode
 * @property string|null $jadw_tim_posisi
 * @property string|null $jadw_tim_kesanggupan
 * @property Carbon|null $jadw_tim_kesanggupan_tgl
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterPegawai $master_pegawai
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisJadwalTim extends Model
{
	protected $table = 'sis_jadwal_tim';
	protected $primaryKey = 'jadw_tim_id';

	protected $casts = [
		'jadw_id' => 'int',
		'peg_id' => 'int'
	];

	protected $dates = [
		'jadw_tim_kesanggupan_tgl'
	];

	protected $fillable = [
		'jadw_id',
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

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

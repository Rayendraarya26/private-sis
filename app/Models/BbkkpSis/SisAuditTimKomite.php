<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTimKomite
 * 
 * @property int $komite_id
 * @property int $jadw_id
 * @property int $peg_id
 * @property string $komite_posisi
 * @property Carbon|null $komite_tgl_surat
 * @property string|null $komite_kesanggupan
 * @property Carbon|null $komite_tgl_kesanggupan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterPegawai $master_pegawai
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTimKomite extends Model
{
	protected $table = 'sis_audit_tim_komite';
	protected $primaryKey = 'komite_id';

	protected $casts = [
		'jadw_id' => 'int',
		'peg_id' => 'int'
	];

	protected $dates = [
		'komite_tgl_surat',
		'komite_tgl_kesanggupan'
	];

	protected $fillable = [
		'jadw_id',
		'peg_id',
		'komite_posisi',
		'komite_tgl_surat',
		'komite_kesanggupan',
		'komite_tgl_kesanggupan'
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

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditTahap1Tim
 * 
 * @property int $thp1_tim_id
 * @property int $aud_thp1_id
 * @property int $peg_id
 * @property string|null $thp1_tim_kode
 * @property string $thp1_tim_posisi
 * @property string|null $thp1_tim_kesanggupan
 * @property Carbon|null $thp1_tim_kesanggupan_tgl
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterPegawai $master_pegawai
 * @property SisAuditDetailTahap1 $sis_audit_detail_tahap1
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditTahap1Tim extends Model
{
	protected $table = 'sis_audit_tahap1_tim';
	protected $primaryKey = 'thp1_tim_id';

	protected $casts = [
		'aud_thp1_id' => 'int',
		'peg_id' => 'int'
	];

	protected $dates = [
		'thp1_tim_kesanggupan_tgl'
	];

	protected $fillable = [
		'aud_thp1_id',
		'peg_id',
		'thp1_tim_kode',
		'thp1_tim_posisi',
		'thp1_tim_kesanggupan',
		'thp1_tim_kesanggupan_tgl'
	];

	public function master_pegawai()
	{
		return $this->belongsTo(MasterPegawai::class, 'peg_id');
	}

	public function sis_audit_detail_tahap1()
	{
		return $this->belongsTo(SisAuditDetailTahap1::class, 'aud_thp1_id');
	}
}

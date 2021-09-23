<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditKomitePeriksa
 * 
 * @property int $komte_priksa_id
 * @property int $jadw_id
 * @property string|null $komte_priksa_penilaian_1
 * @property string|null $komte_priksa_penilaian_2
 * @property string|null $komte_priksa_penilaian_3
 * @property string|null $komte_priksa_penilaian_4
 * @property string|null $komte_priksa_penilaian_5
 * @property string|null $komte_priksa_penilaian_6
 * @property string|null $komte_priksa_penilaian_7
 * @property string|null $komte_priksa_penilaian_8
 * @property string|null $komte_priksa_penilaian_9
 * @property string|null $komte_priksa_penilaian_10
 * @property string|null $komte_priksa_penilaian_11
 * @property string|null $komte_priksa_penilaian_12
 * @property string|null $komte_priksa_penilaian_13
 * @property string|null $komte_priksa_keputusan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditKomitePeriksa extends Model
{
	protected $table = 'sis_audit_komite_periksa';
	protected $primaryKey = 'komte_priksa_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $fillable = [
		'jadw_id',
		'komte_priksa_penilaian_1',
		'komte_priksa_penilaian_2',
		'komte_priksa_penilaian_3',
		'komte_priksa_penilaian_4',
		'komte_priksa_penilaian_5',
		'komte_priksa_penilaian_6',
		'komte_priksa_penilaian_7',
		'komte_priksa_penilaian_8',
		'komte_priksa_penilaian_9',
		'komte_priksa_penilaian_10',
		'komte_priksa_penilaian_11',
		'komte_priksa_penilaian_12',
		'komte_priksa_penilaian_13',
		'komte_priksa_keputusan'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

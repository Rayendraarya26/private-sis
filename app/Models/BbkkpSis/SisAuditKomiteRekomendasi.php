<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditKomiteRekomendasi
 * 
 * @property int $rekmd_komte_id
 * @property int $jadw_id
 * @property string $rekmd_komte_status
 * @property string|null $rekmd_komte_isi
 * @property string|null $rekmd_komte_kronologin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 * @property Collection|SisAuditKomiteRekomendasiFiles[] $sis_audit_komite_rekomendasi_files
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
		'rekmd_komte_status',
		'rekmd_komte_isi',
		'rekmd_komte_kronologin'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}

	public function sis_audit_komite_rekomendasi_files()
	{
		return $this->hasMany(SisAuditKomiteRekomendasiFiles::class, 'rekmd_komte_id');
	}
}

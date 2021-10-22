<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditLks
 * 
 * @property int $lks_id
 * @property int $jadw_audit_id
 * @property int $user_id
 * @property string|null $jadw_team_kode
 * @property string|null $lks_uraian_ketidaksesuaian
 * @property string|null $lks_kategori_ketidaksesuaian
 * @property string|null $lks_klausul_ketidaksesuaian
 * @property string|null $lks_perbaikan_analisa
 * @property string|null $lks_perbaikan_koreksi
 * @property string|null $lks_perbaikan_tindakan
 * @property string|null $lks_bagian_pendamping
 * @property string|null $lks_bukti_tindakan_perbaikan
 * @property Carbon|null $lks_expired_date_perbaikan
 * @property Carbon|null $lks_input_date_perbaikan
 * @property string|null $lks_status
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwalAudit $sis_jadwal_audit
 * @property SysUser $sys_user
 * @property Collection|SisAuditLksFile[] $sis_audit_lks_files
 * @property Collection|SisAuditLksRevisi[] $sis_audit_lks_revisis
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditLks extends Model
{
	protected $table = 'sis_audit_lks';
	protected $primaryKey = 'lks_id';

	protected $casts = [
		'jadw_audit_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'lks_expired_date_perbaikan',
		'lks_input_date_perbaikan'
	];

	protected $fillable = [
		'jadw_audit_id',
		'user_id',
		'jadw_team_kode',
		'lks_uraian_ketidaksesuaian',
		'lks_kategori_ketidaksesuaian',
		'lks_klausul_ketidaksesuaian',
		'lks_perbaikan_analisa',
		'lks_perbaikan_koreksi',
		'lks_perbaikan_tindakan',
		'lks_bagian_pendamping',
		'lks_bukti_tindakan_perbaikan',
		'lks_expired_date_perbaikan',
		'lks_input_date_perbaikan',
		'lks_status'
	];

	public function sis_jadwal_audit()
	{
		return $this->belongsTo(SisJadwalAudit::class, 'jadw_audit_id');
	}

	public function sys_user()
	{
		return $this->belongsTo(SysUser::class, 'user_id');
	}

	public function sis_audit_lks_files()
	{
		return $this->hasMany(SisAuditLksFile::class, 'lks_id');
	}

	public function sis_audit_lks_revisis()
	{
		return $this->hasMany(SisAuditLksRevisi::class, 'lks_id');
	}
}

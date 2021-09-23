<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterPegawai
 * 
 * @property int $peg_id
 * @property int $user_id
 * @property string $peg_nama
 * @property string|null $peg_alamat
 * @property string|null $peg_telp
 * @property string|null $peg_ttd_file
 * @property string $peg_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SysUser $sys_user
 * @property Collection|SisAuditTimKomite[] $sis_audit_tim_komites
 * @property Collection|SisJadwalTim[] $sis_jadwal_tims
 *
 * @package App\Models\BbkkpSis
 */
class MasterPegawai extends Model
{
	protected $table = 'master_pegawai';
	protected $primaryKey = 'peg_id';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'peg_nama',
		'peg_alamat',
		'peg_telp',
		'peg_ttd_file',
		'peg_status'
	];

	public function sys_user()
	{
		return $this->belongsTo(SysUser::class, 'user_id');
	}

	public function sis_audit_tim_komites()
	{
		return $this->hasMany(SisAuditTimKomite::class, 'peg_id');
	}

	public function sis_jadwal_tims()
	{
		return $this->hasMany(SisJadwalTim::class, 'peg_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiKompetensiKomite
 * 
 * @property int $pegkomkomite_id
 * @property int|null $peg_id
 * @property int|null $sert_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_id
 * @property int|null $updated_id
 * 
 * @property MasterPegawai|null $master_pegawai
 * @property MasterSertifikasi|null $master_sertifikasi
 *
 * @package App\Models\BbkkpSis
 */
class PegawaiKompetensiKomite extends Model
{
	protected $table = 'pegawai_kompetensi_komite';
	protected $primaryKey = 'pegkomkomite_id';

	protected $casts = [
		'peg_id' => 'int',
		'sert_id' => 'int',
		'created_id' => 'int',
		'updated_id' => 'int'
	];

	protected $fillable = [
		'peg_id',
		'sert_id',
		'created_id',
		'updated_id'
	];

	public function master_pegawai()
	{
		return $this->belongsTo(MasterPegawai::class, 'peg_id');
	}

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}
}

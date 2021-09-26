<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterSertifikasiDokuman
 * 
 * @property int $sert_dok_id
 * @property int $jenis_dok_perusahaan_id
 * @property int $sert_id
 * @property string|null $sert_dok_keterangan
 * @property string|null $sert_dok_required
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterJenisDokPerusahaan $master_jenis_dok_perusahaan
 * @property MasterSertifikasi $master_sertifikasi
 *
 * @package App\Models\BbkkpSis
 */
class MasterSertifikasiDokuman extends Model
{
	protected $table = 'master_sertifikasi_dokumen';
	protected $primaryKey = 'sert_dok_id';

	protected $casts = [
		'jenis_dok_perusahaan_id' => 'int',
		'sert_id' => 'int'
	];

	protected $fillable = [
		'jenis_dok_perusahaan_id',
		'sert_id',
		'sert_dok_keterangan',
		'sert_dok_required'
	];

	public function master_jenis_dok_perusahaan()
	{
		return $this->belongsTo(MasterJenisDokPerusahaan::class, 'jenis_dok_perusahaan_id');
	}

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}
}

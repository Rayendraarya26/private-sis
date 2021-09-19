<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanSertifikasi
 * 
 * @property int $req_sert_id
 * @property int $cust_id
 * @property int $sert_id
 * @property string $req_sert_approved
 * @property string|null $req_sert_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterSertifikasi $master_sertifikasi
 * @property SisPelanggan $sis_pelanggan
 * @property Collection|SisPelangganSertifikasi[] $sis_pelanggan_sertifikasis
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanSertifikasi extends Model
{
	protected $table = 'sis_permohonan_sertifikasi';
	protected $primaryKey = 'req_sert_id';

	protected $casts = [
		'cust_id' => 'int',
		'sert_id' => 'int'
	];

	protected $fillable = [
		'cust_id',
		'sert_id',
		'req_sert_approved',
		'req_sert_status'
	];

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}

	public function sis_pelanggan_sertifikasis()
	{
		return $this->hasMany(SisPelangganSertifikasi::class, 'req_sert_id');
	}
}

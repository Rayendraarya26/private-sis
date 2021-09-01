<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPelangganSertifikasi
 * 
 * @property int $cust_sert_id
 * @property int $sert_id
 * @property int $cust_id
 * @property string|null $cust_sert_status
 * @property Carbon $cust_sert_expired_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterSertifikasi $master_sertifikasi
 * @property SisPelanggan $sis_pelanggan
 *
 * @package App\Models\BbkkpSis
 */
class SisPelangganSertifikasi extends Model
{
	protected $table = 'sis_pelanggan_sertifikasi';
	protected $primaryKey = 'cust_sert_id';
	public $incrementing = false;

	protected $casts = [
		'cust_sert_id' => 'int',
		'sert_id' => 'int',
		'cust_id' => 'int'
	];

	protected $dates = [
		'cust_sert_expired_date'
	];

	protected $fillable = [
		'sert_id',
		'cust_id',
		'cust_sert_status',
		'cust_sert_expired_date'
	];

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}
}

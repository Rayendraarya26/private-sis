<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisBillingItems
 * 
 * @property int $itms_bil_id
 * @property int $bill_id
 * @property string $itms_bil_tipe
 * @property int|null $mohon_id
 * @property int|null $mohon_det_id
 * @property int|null $cust_sert_id
 * @property string|null $itms_bil_desc
 * @property float|null $itms_bil_total
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property SisBilling $sis_billing
 * @property SisPelangganSertifikasi|null $sis_pelanggan_sertifikasi
 * @property SisPermohonan|null $sis_permohonan
 * @property SisPermohonanDetail|null $sis_permohonan_detail
 *
 * @package App\Models\BbkkpSis
 */
class SisBillingItems extends Model
{
	protected $table = 'sis_billing_items';
	protected $primaryKey = 'itms_bil_id';

	protected $casts = [
		'bill_id' => 'int',
		'mohon_id' => 'int',
		'mohon_det_id' => 'int',
		'cust_sert_id' => 'int',
		'itms_bil_total' => 'float'
	];

	protected $fillable = [
		'bill_id',
		'itms_bil_tipe',
		'mohon_id',
		'mohon_det_id',
		'cust_sert_id',
		'itms_bil_desc',
		'itms_bil_total'
	];

	public function sis_billing()
	{
		return $this->belongsTo(SisBilling::class, 'bill_id');
	}

	public function sis_pelanggan_sertifikasi()
	{
		return $this->belongsTo(SisPelangganSertifikasi::class, 'cust_sert_id');
	}

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}

	public function sis_permohonan_detail()
	{
		return $this->belongsTo(SisPermohonanDetail::class, 'mohon_det_id');
	}
}

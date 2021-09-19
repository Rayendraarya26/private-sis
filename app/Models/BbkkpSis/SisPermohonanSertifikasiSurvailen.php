<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanSertifikasiSurvailen
 * 
 * @property int|null $surv_id
 * @property int|null $surv_req_sert_id
 * @property Carbon|null $surv_tanggal
 * @property Carbon|null $surv_created_at
 * @property Carbon|null $surv_updated_at
 * @property int|null $surv_created_id
 * @property int|null $surv_updated_id
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanSertifikasiSurvailen extends Model
{
	protected $table = 'sis_permohonan_sertifikasi_survailen';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'surv_id' => 'int',
		'surv_req_sert_id' => 'int',
		'surv_created_id' => 'int',
		'surv_updated_id' => 'int'
	];

	protected $dates = [
		'surv_tanggal',
		'surv_created_at',
		'surv_updated_at'
	];

	protected $fillable = [
		'surv_id',
		'surv_req_sert_id',
		'surv_tanggal',
		'surv_created_at',
		'surv_updated_at',
		'surv_created_id',
		'surv_updated_id'
	];
}

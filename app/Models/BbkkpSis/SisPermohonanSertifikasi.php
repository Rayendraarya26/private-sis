<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanSertifikasi
 * 
 * @property int $req_sert_id
 * @property int $cust_id
 * @property int $sert_id
 * @property string $req_sert_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
		'req_sert_status'
	];
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPelangganSurveilan
 * 
 * @property int $cust_survei_id
 * @property int $cust_id
 * @property int $cust_sert_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class SisPelangganSurveilan extends Model
{
	protected $table = 'sis_pelanggan_surveilan';
	protected $primaryKey = 'cust_survei_id';

	protected $casts = [
		'cust_id' => 'int',
		'cust_sert_id' => 'int'
	];

	protected $fillable = [
		'cust_id',
		'cust_sert_id'
	];
}

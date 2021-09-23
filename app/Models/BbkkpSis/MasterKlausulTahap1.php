<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKlausulTahap1
 * 
 * @property int $klausul_thp1_id
 * @property int|null $sert_id
 * @property string|null $klausul_thp1_nomor
 * @property string|null $klausul_thp1_peryataan
 * @property string|null $klausul_thp1_is_tinjauan
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterKlausulTahap1 extends Model
{
	protected $table = 'master_klausul_tahap1';
	protected $primaryKey = 'klausul_thp1_id';

	protected $casts = [
		'sert_id' => 'int'
	];

	protected $fillable = [
		'sert_id',
		'klausul_thp1_nomor',
		'klausul_thp1_peryataan',
		'klausul_thp1_is_tinjauan'
	];
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterSertifikasi
 * 
 * @property int $sert_id
 * @property string|null $sert_nama
 * @property string|null $sert_deskripsi
 * @property int|null $sert_expired
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterSertifikasi extends Model
{
	protected $table = 'master_sertifikasi';
	protected $primaryKey = 'sert_id';

	protected $casts = [
		'sert_expired' => 'int'
	];

	protected $fillable = [
		'sert_nama',
		'sert_deskripsi',
		'sert_expired'
	];
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterSertifikasiKlausul
 * 
 * @property int $sert_klau_id
 * @property int $sert_id
 * @property string|null $sert_klau_nomor
 * @property string|null $sert_klau_peryataan
 * @property string|null $sert_klau_is_item
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterSertifikasi $master_sertifikasi
 *
 * @package App\Models\BbkkpSis
 */
class MasterSertifikasiKlausul extends Model
{
	protected $table = 'master_sertifikasi_klausul';
	protected $primaryKey = 'sert_klau_id';

	protected $casts = [
		'sert_id' => 'int'
	];

	protected $fillable = [
		'sert_id',
		'sert_klau_nomor',
		'sert_klau_peryataan',
		'sert_klau_is_item'
	];

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}
}

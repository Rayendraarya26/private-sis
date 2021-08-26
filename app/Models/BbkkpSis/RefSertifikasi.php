<?php


namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefSertifikasi
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
class RefSertifikasi extends Model
{
	protected $table = 'ref_sertifikasi';
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

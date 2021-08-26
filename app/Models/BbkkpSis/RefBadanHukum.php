<?php


namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefBadanHukum
 *
 * @property int $badan_hukum_id
 * @property string|null $badan_hukum_nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class RefBadanHukum extends Model
{
	protected $table = 'ref_badan_hukum';
	protected $primaryKey = 'badan_hukum_id';

	protected $fillable = [
		'badan_hukum_nama'
	];
}

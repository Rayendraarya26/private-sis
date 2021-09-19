<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterNegara
 * 
 * @property int $negara_id
 * @property string|null $negara_nama
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterNegara extends Model
{
	protected $table = 'master_negara';
	protected $primaryKey = 'negara_id';

	protected $fillable = [
		'negara_nama'
	];
}

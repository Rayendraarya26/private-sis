<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKomoditi
 * 
 * @property int $komodt_id
 * @property string|null $komodt_nama
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterKomoditi extends Model
{
	protected $table = 'master_komoditi';
	protected $primaryKey = 'komodt_id';

	protected $fillable = [
		'komodt_nama'
	];
}

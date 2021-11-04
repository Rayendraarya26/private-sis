<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterRuangLingkup
 * 
 * @property int $ruang_ling_id
 * @property string|null $ruang_ling_nama
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterRuangLingkup extends Model
{
	protected $table = 'master_ruang_lingkup';
	protected $primaryKey = 'ruang_ling_id';

	protected $fillable = [
		'ruang_ling_nama'
	];
}

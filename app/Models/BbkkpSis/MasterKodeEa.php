<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKodeEa
 * 
 * @property int $kode_ea_id
 * @property string|null $kode_ea_nama
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterKodeEa extends Model
{
	protected $table = 'master_kode_ea';
	protected $primaryKey = 'kode_ea_id';

	protected $fillable = [
		'kode_ea_nama'
	];
}

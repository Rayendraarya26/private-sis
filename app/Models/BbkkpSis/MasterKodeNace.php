<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKodeNace
 * 
 * @property int $kode_nace_id
 * @property string|null $kode_nace_nama
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterKodeNace extends Model
{
	protected $table = 'master_kode_nace';
	protected $primaryKey = 'kode_nace_id';

	protected $fillable = [
		'kode_nace_nama'
	];
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKodepos
 * 
 * @property int $id
 * @property string $kelurahan
 * @property string $kecamatan
 * @property string $kabupaten
 * @property string $provinsi
 * @property string $kodepos
 *
 * @package App\Models\BbkkpSis
 */
class MasterKodepos extends Model
{
	protected $table = 'master_kodepos';
	public $timestamps = false;

	protected $fillable = [
		'kelurahan',
		'kecamatan',
		'kabupaten',
		'provinsi',
		'kodepos'
	];
}

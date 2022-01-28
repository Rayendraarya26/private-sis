<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiKompetensiPpc
 * 
 * @property int $pegkomppc_id
 * @property int|null $peg_id
 * @property int|null $komodt_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_id
 * @property int|null $updated_id
 * 
 * @property MasterPegawai|null $master_pegawai
 * @property MasterKomoditi|null $master_komoditi
 *
 * @package App\Models\BbkkpSis
 */
class PegawaiKompetensiPpc extends Model
{
	protected $table = 'pegawai_kompetensi_ppc';
	protected $primaryKey = 'pegkomppc_id';

	protected $casts = [
		'peg_id' => 'int',
		'komodt_id' => 'int',
		'created_id' => 'int',
		'updated_id' => 'int'
	];

	protected $fillable = [
		'peg_id',
		'komodt_id',
		'created_id',
		'updated_id'
	];

	public function master_pegawai()
	{
		return $this->belongsTo(MasterPegawai::class, 'peg_id');
	}

	public function master_komoditi()
	{
		return $this->belongsTo(MasterKomoditi::class, 'komodt_id');
	}
}

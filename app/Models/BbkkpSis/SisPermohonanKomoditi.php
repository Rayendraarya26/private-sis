<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanKomoditi
 * 
 * @property int $mohon_kmditi_id
 * @property int $mohon_id
 * @property int $komodt_id
 * @property string|null $mohon_kmditi_sni
 * @property string|null $mohon_kmditi_merk
 * @property string|null $mohon_kmditi_tipe
 * @property string|null $mohon_kmditi_ukuran
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterKomoditi $master_komoditi
 * @property SisPermohonan $sis_permohonan
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanKomoditi extends Model
{
	protected $table = 'sis_permohonan_komoditi';
	protected $primaryKey = 'mohon_kmditi_id';
	public $incrementing = false;

	protected $casts = [
		'mohon_kmditi_id' => 'int',
		'mohon_id' => 'int',
		'komodt_id' => 'int'
	];

	protected $fillable = [
		'mohon_id',
		'komodt_id',
		'mohon_kmditi_sni',
		'mohon_kmditi_merk',
		'mohon_kmditi_tipe',
		'mohon_kmditi_ukuran'
	];

	public function master_komoditi()
	{
		return $this->belongsTo(MasterKomoditi::class, 'komodt_id');
	}

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}
}

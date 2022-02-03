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
 * @property int $mohon_det_id
 * @property int $komodt_id
 * @property string|null $mohon_kmditi_sni
 * @property string|null $mohon_kmditi_merk
 * @property string|null $mohon_kmditi_tipe
 * @property string|null $mohon_kmditi_ukuran
 * @property string|null $mohon_kmditi_nace
 * @property string|null $mohon_kmditi_ea
 * @property string|null $mohon_kmditi_ruang_lingkup
 * @property string|null $mohon_kmditi_kapasitas_produksi_tahunan
 * @property string|null $mohon_kmditi_kapasitas_produksi_tahunan_satuan
 * @property string|null $mohon_kmditi_keterangan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterKomoditi $master_komoditi
 * @property SisPermohonanDetail $sis_permohonan_detail
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanKomoditi extends Model
{
	protected $table = 'sis_permohonan_komoditi';
	protected $primaryKey = 'mohon_kmditi_id';

	protected $casts = [
		'mohon_det_id' => 'int',
		'komodt_id' => 'int'
	];

	protected $fillable = [
		'mohon_det_id',
		'komodt_id',
		'mohon_kmditi_sni',
		'mohon_kmditi_merk',
		'mohon_kmditi_tipe',
		'mohon_kmditi_ukuran',
		'mohon_kmditi_nace',
		'mohon_kmditi_ea',
		'mohon_kmditi_ruang_lingkup',
		'mohon_kmditi_kapasitas_produksi_tahunan',
		'mohon_kmditi_kapasitas_produksi_tahunan_satuan',
		'mohon_kmditi_keterangan'
	];

	public function master_komoditi()
	{
		return $this->belongsTo(MasterKomoditi::class, 'komodt_id');
	}

	public function sis_permohonan_detail()
	{
		return $this->belongsTo(SisPermohonanDetail::class, 'mohon_det_id');
	}
}

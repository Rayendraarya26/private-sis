<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanJawaban
 * 
 * @property int $mohon_jawab_id
 * @property int $mohon_cust_id
 * @property int $tanya_mohon_id
 * @property string|null $mohon_jawab_jawaban
 * @property float|null $mohon_jawab_score
 * @property int|null $tanya_jwb_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterPertanyaanPermohonan $master_pertanyaan_permohonan
 * @property SisPermohonanPelanggan $sis_permohonan_pelanggan
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanJawaban extends Model
{
	protected $table = 'sis_permohonan_jawaban';
	protected $primaryKey = 'mohon_jawab_id';
	public $incrementing = false;

	protected $casts = [
		'mohon_jawab_id' => 'int',
		'mohon_cust_id' => 'int',
		'tanya_mohon_id' => 'int',
		'mohon_jawab_score' => 'float',
		'tanya_jwb_id' => 'int'
	];

	protected $fillable = [
		'mohon_cust_id',
		'tanya_mohon_id',
		'mohon_jawab_jawaban',
		'mohon_jawab_score',
		'tanya_jwb_id'
	];

	public function master_pertanyaan_permohonan()
	{
		return $this->belongsTo(MasterPertanyaanPermohonan::class, 'tanya_mohon_id');
	}

	public function sis_permohonan_pelanggan()
	{
		return $this->belongsTo(SisPermohonanPelanggan::class, 'mohon_cust_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterPertanyaanPermohonanJawaban
 * 
 * @property int $tanya_jwb_id
 * @property int|null $tanya_mohon_id
 * @property int|null $tanya_jwb_urut
 * @property string|null $tanya_jwb_text
 * @property float|null $tanya_jwb_score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterPertanyaanPermohonan|null $master_pertanyaan_permohonan
 *
 * @package App\Models\BbkkpSis
 */
class MasterPertanyaanPermohonanJawaban extends Model
{
	protected $table = 'master_pertanyaan_permohonan_jawaban';
	protected $primaryKey = 'tanya_jwb_id';

	protected $casts = [
		'tanya_mohon_id' => 'int',
		'tanya_jwb_urut' => 'int',
		'tanya_jwb_score' => 'float'
	];

	protected $fillable = [
		'tanya_mohon_id',
		'tanya_jwb_urut',
		'tanya_jwb_text',
		'tanya_jwb_score'
	];

	public function master_pertanyaan_permohonan()
	{
		return $this->belongsTo(MasterPertanyaanPermohonan::class, 'tanya_mohon_id');
	}
}

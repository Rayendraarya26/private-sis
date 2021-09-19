<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterPertanyaanPermohonan
 * 
 * @property int $tanya_mohon_id
 * @property int|null $tanya_mohon_urut
 * @property string|null $tanya_mohon_pertanyaan
 * @property string|null $tanya_mohon_jenis_jawaban
 * @property string|null $tanya_mohon_status
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|MasterPertanyaanPermohonanJawaban[] $master_pertanyaan_permohonan_jawabans
 * @property Collection|SisPermohonanJawaban[] $sis_permohonan_jawabans
 *
 * @package App\Models\BbkkpSis
 */
class MasterPertanyaanPermohonan extends Model
{
	protected $table = 'master_pertanyaan_permohonan';
	protected $primaryKey = 'tanya_mohon_id';

	protected $casts = [
		'tanya_mohon_urut' => 'int'
	];

	protected $fillable = [
		'tanya_mohon_urut',
		'tanya_mohon_pertanyaan',
		'tanya_mohon_jenis_jawaban',
		'tanya_mohon_status'
	];

	public function master_pertanyaan_permohonan_jawabans()
	{
		return $this->hasMany(MasterPertanyaanPermohonanJawaban::class, 'tanya_mohon_id');
	}

	public function sis_permohonan_jawabans()
	{
		return $this->hasMany(SisPermohonanJawaban::class, 'tanya_mohon_id');
	}
}

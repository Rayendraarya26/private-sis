<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanDokuman
 * 
 * @property int $mohon_dok_id
 * @property int|null $mohon_cust_id
 * @property int|null $jenis_dok_perusahaan_id
 * @property string|null $mohon_dok_deskripsi
 * @property string|null $mohon_dok_filepath
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property SisPermohonanPelanggan|null $sis_permohonan_pelanggan
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanDokuman extends Model
{
	protected $table = 'sis_permohonan_dokumen';
	protected $primaryKey = 'mohon_dok_id';

	protected $casts = [
		'mohon_cust_id' => 'int',
		'jenis_dok_perusahaan_id' => 'int'
	];

	protected $fillable = [
		'mohon_cust_id',
		'jenis_dok_perusahaan_id',
		'mohon_dok_deskripsi',
		'mohon_dok_filepath'
	];

	public function sis_permohonan_pelanggan()
	{
		return $this->belongsTo(SisPermohonanPelanggan::class, 'mohon_cust_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPelangganDokuman
 * 
 * @property int $cust_dok_id
 * @property int|null $cust_id
 * @property int|null $jenis_dok_perusahaan_id
 * @property string|null $cust_dok_deskripsi
 * @property string|null $cust_dok_filepath
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterJenisDokPerusahaan|null $master_jenis_dok_perusahaan
 * @property SisPelanggan|null $sis_pelanggan
 *
 * @package App\Models\BbkkpSis
 */
class SisPelangganDokuman extends Model
{
	protected $table = 'sis_pelanggan_dokumen';
	protected $primaryKey = 'cust_dok_id';

	protected $casts = [
		'cust_id' => 'int',
		'jenis_dok_perusahaan_id' => 'int'
	];

	protected $fillable = [
		'cust_id',
		'jenis_dok_perusahaan_id',
		'cust_dok_deskripsi',
		'cust_dok_filepath'
	];

	public function master_jenis_dok_perusahaan()
	{
		return $this->belongsTo(MasterJenisDokPerusahaan::class, 'jenis_dok_perusahaan_id');
	}

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}
}

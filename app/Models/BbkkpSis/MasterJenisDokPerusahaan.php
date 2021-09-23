<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterJenisDokPerusahaan
 * 
 * @property int $jenis_dok_perusahaan_id
 * @property string|null $jenis_dok_perusahaan_text
 * @property string|null $jenis_dok_perusahaan_deskripsi
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|MasterSertifikasiDokuman[] $master_sertifikasi_dokumen
 * @property Collection|SisPelangganDokuman[] $sis_pelanggan_dokumen
 *
 * @package App\Models\BbkkpSis
 */
class MasterJenisDokPerusahaan extends Model
{
	protected $table = 'master_jenis_dok_perusahaan';
	protected $primaryKey = 'jenis_dok_perusahaan_id';

	protected $fillable = [
		'jenis_dok_perusahaan_text',
		'jenis_dok_perusahaan_deskripsi'
	];

	public function master_sertifikasi_dokumen()
	{
		return $this->hasMany(MasterSertifikasiDokuman::class, 'jenis_dok_perusahaan_id');
	}

	public function sis_pelanggan_dokumen()
	{
		return $this->hasMany(SisPelangganDokuman::class, 'jenis_dok_perusahaan_id');
	}
}

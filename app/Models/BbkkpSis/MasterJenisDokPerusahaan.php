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
 * @property string|null $jenis_dok_perusahaan_sample_file
 * @property string|null $jenis_dok_perusahaan_deskripsi
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|MasterSertifikasiDokumen[] $master_sertifikasi_dokumens
 * @property Collection|SisPelangganDokumen[] $sis_pelanggan_dokumens
 * @property Collection|SisPermohonanDokumen[] $sis_permohonan_dokumens
 *
 * @package App\Models\BbkkpSis
 */
class MasterJenisDokPerusahaan extends Model
{
	protected $table = 'master_jenis_dok_perusahaan';
	protected $primaryKey = 'jenis_dok_perusahaan_id';

	protected $fillable = [
		'jenis_dok_perusahaan_text',
		'jenis_dok_perusahaan_sample_file',
		'jenis_dok_perusahaan_deskripsi'
	];

	public function master_sertifikasi_dokumens()
	{
		return $this->hasMany(MasterSertifikasiDokumen::class, 'jenis_dok_perusahaan_id');
	}

	public function sis_pelanggan_dokumens()
	{
		return $this->hasMany(SisPelangganDokumen::class, 'jenis_dok_perusahaan_id');
	}

	public function sis_permohonan_dokumens()
	{
		return $this->hasMany(SisPermohonanDokumen::class, 'jenis_dok_perusahaan_id');
	}
}

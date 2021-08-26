<?php


namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefJenisPerusahaan
 *
 * @property int $jenis_perusahaan_id
 * @property string|null $jenis_perusahaan_nama
 * @property string|null $jenis_perusahaan_deskripsi
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class RefJenisPerusahaan extends Model
{
	protected $table = 'ref_jenis_perusahaan';
	protected $primaryKey = 'jenis_perusahaan_id';

	protected $fillable = [
		'jenis_perusahaan_nama',
		'jenis_perusahaan_deskripsi'
	];
}

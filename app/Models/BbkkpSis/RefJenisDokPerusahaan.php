<?php


namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefJenisDokPerusahaan
 *
 * @property int $jenis_dok_perusahaan_id
 * @property string|null $jenis_dok_perusahaan_text
 * @property string|null $jenis_dok_perusahaan_deskripsi
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\BbkkpSis
 */
class RefJenisDokPerusahaan extends Model
{
	protected $table = 'ref_jenis_dok_perusahaan';
	protected $primaryKey = 'jenis_dok_perusahaan_id';

	protected $fillable = [
		'jenis_dok_perusahaan_text',
		'jenis_dok_perusahaan_deskripsi'
	];
}

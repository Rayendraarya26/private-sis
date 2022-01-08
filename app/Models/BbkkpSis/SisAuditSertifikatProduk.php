<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditSertifikatProduk
 * 
 * @property int $prod_sert_id
 * @property int $jadw_id
 * @property Carbon|null $prod_sert_tanggal
 * @property string|null $prod_sert_nomor
 * @property string|null $prod_sert_filepath
 * @property string|null $prod_sert_lab_nama
 * @property string|null $prod_sert_status_hasil
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisJadwal $sis_jadwal
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditSertifikatProduk extends Model
{
	protected $table = 'sis_audit_sertifikat_produk';
	protected $primaryKey = 'prod_sert_id';

	protected $casts = [
		'jadw_id' => 'int'
	];

	protected $dates = [
		'prod_sert_tanggal'
	];

	protected $fillable = [
		'jadw_id',
		'prod_sert_tanggal',
		'prod_sert_nomor',
		'prod_sert_filepath',
		'prod_sert_lab_nama',
		'prod_sert_status_hasil'
	];

	public function sis_jadwal()
	{
		return $this->belongsTo(SisJadwal::class, 'jadw_id');
	}
}

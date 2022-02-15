<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisAuditKomiteRekomendasiFiles
 * 
 * @property int $rekmdfile_id
 * @property int|null $rekmd_komte_id
 * @property string|null $rekmdfile_name
 * @property string|null $rekmdfile_path
 * @property float|null $rekmdfile_size_byte
 * @property string|null $rekmdfile_extension
 * @property Carbon|null $rekmdfile_created_at
 * @property Carbon|null $rekmdfile_updated_at
 * @property int|null $rekmdfile_created_id
 * @property int|null $rekmdfile_updated_id
 * 
 * @property SisAuditKomiteRekomendasi|null $sis_audit_komite_rekomendasi
 *
 * @package App\Models\BbkkpSis
 */
class SisAuditKomiteRekomendasiFiles extends Model
{
	protected $table = 'sis_audit_komite_rekomendasi_files';
	protected $primaryKey = 'rekmdfile_id';
	public $timestamps = false;

	protected $casts = [
		'rekmd_komte_id' => 'int',
		'rekmdfile_size_byte' => 'float',
		'rekmdfile_created_id' => 'int',
		'rekmdfile_updated_id' => 'int'
	];

	protected $dates = [
		'rekmdfile_created_at',
		'rekmdfile_updated_at'
	];

	protected $fillable = [
		'rekmd_komte_id',
		'rekmdfile_name',
		'rekmdfile_path',
		'rekmdfile_size_byte',
		'rekmdfile_extension',
		'rekmdfile_created_at',
		'rekmdfile_updated_at',
		'rekmdfile_created_id',
		'rekmdfile_updated_id'
	];

	public function sis_audit_komite_rekomendasi()
	{
		return $this->belongsTo(SisAuditKomiteRekomendasi::class, 'rekmd_komte_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanPabrik
 * 
 * @property int $mohon_pabrik_id
 * @property int $mohon_id
 * @property string|null $mohon_pabrik_nomor_telp
 * @property string|null $mohon_pabrik_nomor_fax
 * @property string|null $mohon_pabrik_nomor_hp
 * @property string|null $mohon_pabrik_nama
 * @property int|null $negara_id
 * @property int|null $kec_id
 * @property int|null $kab_id
 * @property int|null $prov_id
 * @property string|null $mohon_pabrik_alamat
 * @property string|null $mohon_pabrik_kode_pos
 * @property int|null $mohon_pabrik_jumlah_karyawan
 * @property string|null $mohon_pabrik_kegiatan_utama
 * @property string|null $mohon_pabrik_luas_tanah
 * @property string|null $mohon_pabrik_luas_bangunan
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterKabupaten|null $master_kabupaten
 * @property MasterKecamatan|null $master_kecamatan
 * @property MasterProvinsi|null $master_provinsi
 * @property SisPermohonan $sis_permohonan
 * @property MasterNegara|null $master_negara
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanPabrik extends Model
{
	protected $table = 'sis_permohonan_pabrik';
	protected $primaryKey = 'mohon_pabrik_id';

	protected $casts = [
		'mohon_id' => 'int',
		'negara_id' => 'int',
		'kec_id' => 'int',
		'kab_id' => 'int',
		'prov_id' => 'int',
		'mohon_pabrik_jumlah_karyawan' => 'int'
	];

	protected $fillable = [
		'mohon_id',
		'mohon_pabrik_nomor_telp',
		'mohon_pabrik_nomor_fax',
		'mohon_pabrik_nomor_hp',
		'mohon_pabrik_nama',
		'negara_id',
		'kec_id',
		'kab_id',
		'prov_id',
		'mohon_pabrik_alamat',
		'mohon_pabrik_kode_pos',
		'mohon_pabrik_jumlah_karyawan',
		'mohon_pabrik_kegiatan_utama',
		'mohon_pabrik_luas_tanah',
		'mohon_pabrik_luas_bangunan'
	];

	public function master_kabupaten()
	{
		return $this->belongsTo(MasterKabupaten::class, 'kab_id');
	}

	public function master_kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'kec_id');
	}

	public function master_provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'prov_id');
	}

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}

	public function master_negara()
	{
		return $this->belongsTo(MasterNegara::class, 'negara_id');
	}
}

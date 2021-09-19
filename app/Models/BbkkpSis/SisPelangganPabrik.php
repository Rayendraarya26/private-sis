<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPelangganPabrik
 * 
 * @property int $pabrik_id
 * @property int|null $cust_id
 * @property string|null $pabrik_nomor_telp
 * @property string|null $pabrik_nomor_fax
 * @property string|null $pabrik_nomor_hp
 * @property string|null $pabrik_nama
 * @property int|null $kec_id
 * @property int|null $kab_id
 * @property int|null $prov_id
 * @property string|null $pabrik_alamat
 * @property string|null $pabrik_kode_pos
 * @property int|null $pabrik_jumlah_karyawan
 * @property string|null $pabrik_kegiatan_utama
 * @property string|null $pabrik_luas_tanah
 * @property string|null $pabrik_luas_bangunan
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterKabupaten|null $master_kabupaten
 * @property MasterKecamatan|null $master_kecamatan
 * @property MasterProvinsi|null $master_provinsi
 * @property SisPelanggan|null $sis_pelanggan
 *
 * @package App\Models\BbkkpSis
 */
class SisPelangganPabrik extends Model
{
	protected $table = 'sis_pelanggan_pabrik';
	protected $primaryKey = 'pabrik_id';

	protected $casts = [
		'cust_id' => 'int',
		'kec_id' => 'int',
		'kab_id' => 'int',
		'prov_id' => 'int',
		'pabrik_jumlah_karyawan' => 'int'
	];

	protected $fillable = [
		'cust_id',
		'pabrik_nomor_telp',
		'pabrik_nomor_fax',
		'pabrik_nomor_hp',
		'pabrik_nama',
		'kec_id',
		'kab_id',
		'prov_id',
		'pabrik_alamat',
		'pabrik_kode_pos',
		'pabrik_jumlah_karyawan',
		'pabrik_kegiatan_utama',
		'pabrik_luas_tanah',
		'pabrik_luas_bangunan'
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

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}
}

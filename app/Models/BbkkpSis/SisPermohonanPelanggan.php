<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanPelanggan
 * 
 * @property int $mohon_cust_id
 * @property int $req_sert_id
 * @property int $cust_id
 * @property int $user_id
 * @property string|null $mohon_cust_email
 * @property string|null $mohon_cust_nomor_telp
 * @property string|null $mohon_cust_nomor_fax
 * @property string|null $mohon_cust_nomor_hp
 * @property string|null $mohon_cust_nama
 * @property int|null $jenis_perusahaan_id
 * @property int|null $badan_hukum_id
 * @property string|null $cust_asing
 * @property int|null $negara_id
 * @property int|null $kec_id
 * @property int|null $kab_id
 * @property int|null $prov_id
 * @property string|null $mohon_cust_alamat
 * @property string|null $mohon_cust_nomor_akta_pendirian
 * @property string|null $mohon_cust_nama_pemilik
 * @property string|null $mohon_cust_nama_pimpinan
 * @property string|null $mohon_cust_nama_wakil_manajemen
 * @property int|null $mohon_cust_jumlah_bagian
 * @property int|null $mohon_cust_jumlah_manajemen
 * @property int|null $mohon_cust_jumlah_administrasi
 * @property int|null $mohon_cust_jumlah_part_time
 * @property int|null $mohon_cust_jumlah_operasional
 * @property int|null $mohon_cust_jumlah_shift_1
 * @property int|null $mohon_cust_jumlah_shift_2
 * @property int|null $mohon_cust_jumlah_shift_3
 * @property int|null $mohon_cust_jumlah_non_permanen
 * @property int|null $mohon_cust_shif_kerja
 * @property string|null $mohon_cust_luas_tanah
 * @property string|null $mohon_cust_luas_bangunan
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|SisPermohonanDokuman[] $sis_permohonan_dokumen
 * @property Collection|SisPermohonanJawaban[] $sis_permohonan_jawabans
 * @property Collection|SisPermohonanPabrik[] $sis_permohonan_pabriks
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanPelanggan extends Model
{
	protected $table = 'sis_permohonan_pelanggan';
	protected $primaryKey = 'mohon_cust_id';

	protected $casts = [
		'req_sert_id' => 'int',
		'cust_id' => 'int',
		'user_id' => 'int',
		'jenis_perusahaan_id' => 'int',
		'badan_hukum_id' => 'int',
		'negara_id' => 'int',
		'kec_id' => 'int',
		'kab_id' => 'int',
		'prov_id' => 'int',
		'mohon_cust_jumlah_bagian' => 'int',
		'mohon_cust_jumlah_manajemen' => 'int',
		'mohon_cust_jumlah_administrasi' => 'int',
		'mohon_cust_jumlah_part_time' => 'int',
		'mohon_cust_jumlah_operasional' => 'int',
		'mohon_cust_jumlah_shift_1' => 'int',
		'mohon_cust_jumlah_shift_2' => 'int',
		'mohon_cust_jumlah_shift_3' => 'int',
		'mohon_cust_jumlah_non_permanen' => 'int',
		'mohon_cust_shif_kerja' => 'int'
	];

	protected $fillable = [
		'req_sert_id',
		'cust_id',
		'user_id',
		'mohon_cust_email',
		'mohon_cust_nomor_telp',
		'mohon_cust_nomor_fax',
		'mohon_cust_nomor_hp',
		'mohon_cust_nama',
		'jenis_perusahaan_id',
		'badan_hukum_id',
		'cust_asing',
		'negara_id',
		'kec_id',
		'kab_id',
		'prov_id',
		'mohon_cust_alamat',
		'mohon_cust_nomor_akta_pendirian',
		'mohon_cust_nama_pemilik',
		'mohon_cust_nama_pimpinan',
		'mohon_cust_nama_wakil_manajemen',
		'mohon_cust_jumlah_bagian',
		'mohon_cust_jumlah_manajemen',
		'mohon_cust_jumlah_administrasi',
		'mohon_cust_jumlah_part_time',
		'mohon_cust_jumlah_operasional',
		'mohon_cust_jumlah_shift_1',
		'mohon_cust_jumlah_shift_2',
		'mohon_cust_jumlah_shift_3',
		'mohon_cust_jumlah_non_permanen',
		'mohon_cust_shif_kerja',
		'mohon_cust_luas_tanah',
		'mohon_cust_luas_bangunan'
	];

	public function sis_permohonan_dokumen()
	{
		return $this->hasMany(SisPermohonanDokuman::class, 'mohon_cust_id');
	}

	public function sis_permohonan_jawabans()
	{
		return $this->hasMany(SisPermohonanJawaban::class, 'mohon_cust_id');
	}

	public function sis_permohonan_pabriks()
	{
		return $this->hasMany(SisPermohonanPabrik::class, 'mohon_cust_id');
	}
}

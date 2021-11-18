<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPelangganSertifikasi
 * 
 * @property int $cust_sert_id
 * @property int $sert_id
 * @property int $cust_id
 * @property int|null $mohon_id
 * @property string|null $cust_sert_nomor_sertifikat
 * @property string|null $cust_sert_nomor_referensi
 * @property string|null $cust_sert_nomor_sni
 * @property string|null $cust_sert_lingkup
 * @property string|null $kode_ea_nama
 * @property string|null $kode_nace_nama
 * @property int|null $komodt_id
 * @property string|null $cust_sert_tipe
 * @property string|null $cust_sert_merk
 * @property string|null $cust_sert_ukuran
 * @property float|null $cust_sert_produksi_tahunan
 * @property string|null $cust_sert_produksi_tahunan_satuan
 * @property Carbon|null $cust_sert_tgl_sertifikat_awal
 * @property Carbon|null $cust_sert_tgl_sertifikat_perubahan
 * @property string|null $cust_sert_status
 * @property Carbon $cust_sert_expired_date
 * @property Carbon $cust_sert_survailen_date
 * @property string|null $cust_sert_status_survailen
 * @property string|null $cust_sert_filepath
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property MasterKomoditi|null $master_komoditi
 * @property MasterSertifikasi $master_sertifikasi
 * @property SisPelanggan $sis_pelanggan
 * @property SisPermohonan|null $sis_permohonan
 * @property Collection|SisBillingItems[] $sis_billing_items
 * @property Collection|SisJadwalAudit[] $sis_jadwal_audits
 * @property Collection|SisPermohonan[] $sis_permohonans
 *
 * @package App\Models\BbkkpSis
 */
class SisPelangganSertifikasi extends Model
{
	protected $table = 'sis_pelanggan_sertifikasi';
	protected $primaryKey = 'cust_sert_id';

	protected $casts = [
		'sert_id' => 'int',
		'cust_id' => 'int',
		'mohon_id' => 'int',
		'komodt_id' => 'int',
		'cust_sert_produksi_tahunan' => 'float'
	];

	protected $dates = [
		'cust_sert_tgl_sertifikat_awal',
		'cust_sert_tgl_sertifikat_perubahan',
		'cust_sert_expired_date',
		'cust_sert_survailen_date'
	];

	protected $fillable = [
		'sert_id',
		'cust_id',
		'mohon_id',
		'cust_sert_nomor_sertifikat',
		'cust_sert_nomor_referensi',
		'cust_sert_nomor_sni',
		'cust_sert_lingkup',
		'kode_ea_nama',
		'kode_nace_nama',
		'komodt_id',
		'cust_sert_tipe',
		'cust_sert_merk',
		'cust_sert_ukuran',
		'cust_sert_produksi_tahunan',
		'cust_sert_produksi_tahunan_satuan',
		'cust_sert_tgl_sertifikat_awal',
		'cust_sert_tgl_sertifikat_perubahan',
		'cust_sert_status',
		'cust_sert_expired_date',
		'cust_sert_survailen_date',
		'cust_sert_status_survailen',
		'cust_sert_filepath'
	];

	public function master_komoditi()
	{
		return $this->belongsTo(MasterKomoditi::class, 'komodt_id');
	}

	public function master_sertifikasi()
	{
		return $this->belongsTo(MasterSertifikasi::class, 'sert_id');
	}

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}

	public function sis_permohonan()
	{
		return $this->belongsTo(SisPermohonan::class, 'mohon_id');
	}

	public function sis_billing_items()
	{
		return $this->hasMany(SisBillingItems::class, 'cust_sert_id');
	}

	public function sis_jadwal_audits()
	{
		return $this->hasMany(SisJadwalAudit::class, 'cust_sert_id');
	}

	public function sis_permohonans()
	{
		return $this->hasMany(SisPermohonan::class, 'cust_sert_id');
	}
}

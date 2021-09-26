<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterSertifikasi
 *
 * @property int $sert_id
 * @property string|null $sert_nama
 * @property string|null $sert_deskripsi
 * @property int|null $sert_expired
 * @property string|null $sert_format_referensi
 * @property string|null $sert_is_product
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection|MasterSertifikasiDokuman[] $master_sertifikasi_dokumen
 * @property Collection|MasterSertifikasiKlausul[] $master_sertifikasi_klausuls
 * @property Collection|SisPelangganSertifikasi[] $sis_pelanggan_sertifikasis
 * @property Collection|SisPermohonan[] $sis_permohonans
 *
 * @package App\Models\BbkkpSis
 */
class MasterSertifikasi extends Model
{
    protected $table = 'master_sertifikasi';
    protected $primaryKey = 'sert_id';

    protected $casts = [
        'sert_expired' => 'int',
    ];

    protected $fillable = [
        'sert_nama',
        'sert_deskripsi',
        'sert_expired',
        'sert_format_referensi',
        'sert_is_product'
    ];

    public function master_sertifikasi_dokumen()
    {
        return $this->hasMany(MasterSertifikasiDokuman::class, 'sert_id');
    }

    public function master_sertifikasi_klausuls()
    {
        return $this->hasMany(MasterSertifikasiKlausul::class, 'sert_id');
    }

    public function sis_pelanggan_sertifikasis()
    {
        return $this->hasMany(SisPelangganSertifikasi::class, 'sert_id');
    }

    public function sis_permohonans()
    {
        return $this->hasMany(SisPermohonan::class, 'sert_id');
    }
}

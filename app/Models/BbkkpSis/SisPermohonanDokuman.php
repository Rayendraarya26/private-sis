<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisPermohonanDokuman
 *
 * @property int $mohon_dok_id
 * @property int $mohon_id
 * @property int|null $jenis_dok_perusahaan_id
 * @property string|null $mohon_dok_deskripsi
 * @property string|null $mohon_dok_filepath
 * @property Carbon|null $created_at
 * @property Carbon $updated_at
 *
 * @property SisPermohonan $sis_permohonan
 * @property MasterJenisDokPerusahaan|null $master_jenis_dok_perusahaan
 *
 * @package App\Models\BbkkpSis
 */
class SisPermohonanDokuman extends Model
{
    protected $table = 'sis_permohonan_dokumen';
    protected $primaryKey = 'mohon_dok_id';

    protected $casts = [
        'mohon_id' => 'int',
        'jenis_dok_perusahaan_id' => 'int'
    ];

    protected $fillable = [
        'mohon_id',
        'jenis_dok_perusahaan_id',
        'mohon_dok_deskripsi',
        'mohon_dok_filepath'
    ];

    public function sis_permohonan()
    {
        return $this->belongsTo(SisPermohonan::class, 'mohon_id');
    }

    public function master_jenis_dok_perusahaan()
    {
        return $this->belongsTo(MasterJenisDokPerusahaan::class, 'jenis_dok_perusahaan_id');
    }
}

<?php

namespace Modules\Pelanggan\Http\Traits;

use App\Exceptions\ExpectedException;
use App\Models\BbkkpSis\SisJadwal;
use Exception;

trait Tahap2Trait
{
    /**
     * @throws Exception
     */
    public function lksMustBeApprove(int $jadwalID)
    {
        $dataJadwal = SisJadwal::with([
            'sis_audit_lks',
            'sis_pelanggan',
            'sis_jadwal_tims.sis_audit_logbook',
            'sis_jadwal_audits',
            'sis_pelanggan',
            'sis_jadwal_tims'
        ])->findOrFail($jadwalID);

        if ($dataJadwal->jadw_setujui_temuan != "setuju") {
            throw new ExpectedException("Anda tidak bisa mengakses halaman ini sekarang");
        }
        return $dataJadwal;
    }
}

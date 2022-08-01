<?php

namespace Modules\Pelanggan\Http\Traits;

use App\Models\BbkkpSis\SisBillingItems;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisPermohonan;

trait PermohonanTrait
{
    // allowCancel memastikan peserta belum menyetujui jadwal permohonan saat penjadwalan
    public function allowCancel(SisPermohonan $dataPemohon): bool
    {
        $dataBilling = SisBillingItems::with('sis_billing')->where('mohon_id', $dataPemohon->mohon_id)->first();
        if (!empty($dataBilling?->sis_billing?->bill_id)) {
            $billingID  = $dataBilling?->sis_billing?->bill_id;
            $dataJadwal = SisJadwal::where('bill_id', $billingID)->first();
            if (!empty($dataJadwal) && $dataJadwal->jadw_tanggal_status == 'accepted') return false;
        }
        return true;
    }
}
